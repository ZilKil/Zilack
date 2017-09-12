<?php

namespace Zilack\Managers;

use Devristo\Phpws\Client\WebSocket;
use Devristo\Phpws\Messaging\MessageInterface;
use GuzzleHttp\Client;
use React\EventLoop\Factory;
use React\Http\Request;
use React\Http\RequestHeaderParser;
use React\Http\Response;
use React\Socket\ConnectionInterface;
use React\Socket\Server;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zilack\Events\CommandEvent;
use Zilack\Events\WebhookEvent;
use Zilack\Services\SlackClient;
use Zilack\ZilackCommand;
use Zilack\ZilackRegistry;
use Zilack\ZilackWebhook;

class SlackManager
{
    const DEFAULT_SLACK_RTM_START_URI = 'https://slack.com/api/rtm.start';

    /** @var ConfigManager $configManager */
    private $configManager;
    private $slackSettings;
    private $context;
    private $loop;
    /** @var  WebSocket $client */
    private $client;
    /** @var  ZilackCommand[] $commands */
    private $commands;
    /** @var  ZilackWebhook[] $webhooks */
    private $webhooks;
    /** @var SlackClient $slackClient */
    private $slackClient;
    private $webhooksConfig;
    private $defaultSocketPort = '8080';
    private $defaultSocketHost = '127.0.0.1';
    /** @var Server */
    private $socket;
    private $socketHost;
    private $socketPort;
    private $http;
    /** @var EventDispatcher */
    private $dispatcher;

    public function __construct()
    {
        $this->dispatcher = ZilackRegistry::get('dispatcher');
        $this->configManager = ZilackRegistry::get('configManager');
        $this->logger = new Logger();
        $this->loop = Factory::create();
        $this->slackSettings = $this->configManager->getConfigParam('slack_settings');
        $this->slackClient = new SlackClient();
    }

    public function init($commands, $webhooks)
    {
        $this->initRTM();
        $this->initOutput();
        $this->setupConnection();
        $this->loadCommands($commands);
        $this->loadWebhooks($webhooks);
        $this->setupMessageListener();
    }

    private function setupMessageListener()
    {
        $this->client->on(
            "message",
            function (MessageInterface $message) {
                $data = $message->getData();

                $this->logger->info($data);
                $data = json_decode($data);
                if (isset($data->text)) {
                    $trigger = explode(' ', $data->text)[0];
                    if (isset($this->commands[$trigger])) {
                        $this->dispatcher->dispatch(
                            'command.received',
                            new CommandEvent($this->commands[$trigger], $data, $this->context, $trigger)
                        );
                    }
                }
            }
        );
    }

    public function openConnection()
    {
        $this->client->open();

        if (false !== $this->webhooksConfig = $this->getWebhooksSettings()) {
            $this->startWebserver($this->webhooksConfig);
            $this->socket->listen($this->socketPort, $this->socketHost);
        }

        $this->loop->run();
    }

    private function loadCommands($commands)
    {
        foreach ($commands as $command) {
            if ($command instanceof ZilackCommand) {
                $command->setClient($this->slackClient);
                $command->configure();
                if (is_array($command->getName())) {
                    foreach ($command->getName() as $name) {
                        $this->commands[$name] = $command;
                    }
                } else {
                    $this->commands[$command->getName()] = $command;
                }
            }
        }
    }

    private function loadWebhooks($webhooks)
    {
        foreach ($webhooks as $webhook) {
            if ($webhook instanceof ZilackWebhook) {
                $webhook->setClient($this->slackClient);
                $webhook->configure();
                $this->webhooks[] = $webhook;
            }
        }
    }

    private function initRTM()
    {
        if (null === $token = $this->slackSettings['token']) {
            throw new \Exception('No Slack token found.', 500);
        }

        $params = ['token' => $token];

        $this->slackClient->setToken($this->slackSettings['token']);
        $response = $this->slackClient->request(
            'GET',
            $this->slackClient->buildSlackUri($params, self::DEFAULT_SLACK_RTM_START_URI)
        );

        $this->context = json_decode($response->getBody()->getContents());

        if (!$this->context->ok) {
            throw new \Exception("An error occurred while comunicating with Slack. [{$this->context->error}]");
        }


    }

    private function initOutput()
    {
        $writer = new Stream("php://output");
        $this->logger->addWriter($writer);
    }

    private function setupConnection()
    {
        $this->client = new WebSocket($this->context->url, $this->loop, $this->logger);

        $this->client->on(
            "request",
            function () {
                $this->logger->notice("Request object created!");
            }
        );

        $this->client->on(
            "handshake",
            function () {
                $this->logger->notice("Handshake received!");
            }
        );

        $this->client->on(
            "connect",
            function () {
                $this->logger->notice("Connected!");
            }
        );
    }

    private function getWebhooksSettings()
    {
        $config = $this->configManager->getZilackConfig();
        if (isset($config['webhooks_enabled']) && $config['webhooks_enabled'] == 1) {
            return [
                'host' => isset($config['webhooks_listener_host']) ? $config['webhooks_listener_host'] : null,
                'port' => isset($config['webhooks_listener_port']) ? $config['webhooks_listener_port'] : null,
            ];
        }

        return false;
    }

    private function startWebserver($webhooksConfig)
    {
        $this->socketHost = is_null($webhooksConfig['host']) ? $this->defaultSocketHost : $webhooksConfig['host'];
        $this->socketPort = is_null($webhooksConfig['port']) ? $this->defaultSocketPort : $webhooksConfig['port'];

        $hostname = sprintf("%s:%s", $this->socketHost, $this->socketPort);

        $this->logger->notice("Webhooks enabled. Listening on {$hostname}");

        $this->socket = new Server($this->loop);
        $this->http = new \React\Http\Server($this->socket);

        $this->socket->on(
            'connection',
            function (ConnectionInterface $conn) {
                $parser = new RequestHeaderParser();
                $parser->on(
                    'headers',
                    function (Request $request, $bodyBuffer) use ($conn) {
                        $response = new Response($conn);
                        $response->writeHead(405, ['Content-Type' => 'text/plain']);
                        $response->end("No content found in request.");
                    }
                );

                $listener = [$parser, 'feed'];
                $conn->on('data', $listener);
            }
        );

        $this->http->on(
            'request',
            function (Request $request, Response $response) {
                if ($request->getMethod() === 'POST') {
                    $request->on(
                        'data',
                        function ($data) use ($request, $response) {
                            try {
                                $data = json_decode($data, true);
                            } catch (\Exception $e) {
                                $data = null;
                            }
                            if (null === $data) {
                                $response->writeHead(500, ['Content-Type' => 'text/plain']);
                                $response->end("No content found in request.");
                            }

                            $webhookEvent = new WebhookEvent();
                            $webhookEvent->setPayload($data);

                            $this->dispatchWebhookEvents($webhookEvent);

                            $response->writeHead(200, ['Content-Type' => 'text/plain']);
                            $response->end("Success");
                        }
                    );
                }
            }
        );

    }

    private function dispatchWebhookEvents(WebhookEvent $webhookEvent)
    {
        foreach ($this->webhooks as $webhook) {
            $event = $webhook->getEvent();
            if (!is_null($event)) {
                $webhookEvent->setWebhook($webhook);
                $this->dispatcher->dispatch($event, $webhookEvent);
            }
        }
    }
}
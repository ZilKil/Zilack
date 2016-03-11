<?php
namespace Zilack;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Zilack\Listeners\CommandListener;
use Zilack\Listeners\WebhookListener;
use Zilack\Managers\SlackManager;

class ZilackBot
{
    /** @var ZilackCommand[] $commands */
    private $commands;
    /** @var  ZilackWebhook[] $webhooks */
    private $webhooks;
    private $configuration;

    public function __construct()
    {
        $this->dispatcher = new EventDispatcher();
        $this->boot();
    }

    public function run()
    {
        $slack = new SlackManager($this->configuration, $this->dispatcher);
        $slack->init($this->commands, $this->webhooks);
        $slack->openConnection();
    }

    public function register($component)
    {
        if($component instanceof ZilackCommand) {
            $this->commands[] = $component;
            return $this;
        } elseif($component instanceof ZilackWebhook) {
            $this->webhooks[] = $component;
            return $this;
        } else {
            throw new \Exception('Component has to extend ZilackCommand or ZilackWebhook class.');
        }
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function addListener($event, $listener, $action)
    {
        if($listener instanceof CommandListener || $listener instanceof WebhookListener){
            $this->dispatcher->addListener($event, [$listener, $action]);
        } else {
            throw new \Exception('Listener has to extend CommandListener or WebhookListener class.');
        }
    }

    private function boot()
    {
        $this->loadInternalListeners();
    }

    private function loadInternalListeners()
    {
        $this->dispatcher->addListener('command.received', [new CommandListener(), 'onCommandReceived']);
        $this->dispatcher->addListener('webhook.received', [new WebhookListener(), 'onWebhookReceived']);
    }
}
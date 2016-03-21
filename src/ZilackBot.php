<?php
namespace Zilack;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Zilack\Listeners\CommandListener;
use Zilack\Listeners\WebhookListener;
use Zilack\Managers\ConfigManager;
use Zilack\Managers\SlackManager;

class ZilackBot
{
    /** @var ZilackCommand[] $commands */
    private $commands;
    /** @var  ZilackWebhook[] $webhooks */
    private $webhooks;
    private $configuration;
    /** @var  EventDispatcher */
    private $dispatcher;

    public function __construct()
    {
        $this->initRegistry();
        $this->dispatcher = ZilackRegistry::get('dispatcher');
        $this->boot();
    }

    public function run()
    {
        $slack = new SlackManager();
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
        ZilackRegistry::get('configManager')->setConfig($configuration);
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
    }

    private function initRegistry()
    {
        ZilackRegistry::add(new ConfigManager(), 'configManager');
        ZilackRegistry::add(new EventDispatcher(), 'dispatcher');
    }
}
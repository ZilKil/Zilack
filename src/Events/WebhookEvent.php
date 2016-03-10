<?php
namespace Zilack\Events;

use Symfony\Component\EventDispatcher\Event;
use Zilack\ZilackCommand;
use Zilack\ZilackWebhook;

class WebhookEvent extends Event
{
    /** @var  ZilackWebhook $webhook */
    private $webhook;
    private $data;
    private $context;

    /**
     * @param mixed $data
     * @return CommandEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return ZilackWebhook
     */
    public function getWebhook()
    {
        return $this->webhook;
    }

    /**
     * @param ZilackWebhook $webhook
     */
    public function setWebhook($webhook)
    {
        $this->webhook = $webhook;
    }
}
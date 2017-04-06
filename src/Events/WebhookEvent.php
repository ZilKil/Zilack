<?php
namespace Zilack\Events;

use React\Http\Request;
use Symfony\Component\EventDispatcher\Event;
use Zilack\ZilackCommand;
use Zilack\ZilackWebhook;

class WebhookEvent extends Event
{
    /** @var  ZilackWebhook $webhook */
    private $webhook;
    private $context;

    /**
     * @var array
     */
    private $payload;

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

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     *
     * @return WebhookEvent
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }
}
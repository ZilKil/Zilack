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
    /** @var  Request $request */
    private $request;

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
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
}
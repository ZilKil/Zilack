<?php

namespace Zilack;

use React\Http\Request;
use Zilack\Services\SlackClient;

abstract class ZilackWebhook
{
    /** @var  SlackClient */
    private $client;
    private $channel;
    private $identity;
    private $event;

    abstract public function configure();
    abstract public function execute(Request $request, $context);

    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return SlackClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param mixed $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $this->getClient()->getConfigManager()->getIdentity($identity);
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }


}
<?php

namespace Zilack;

use Zilack\Services\SlackClient;

abstract class ZilackCommand
{
    private $name;
    /** @var  SlackClient */
    private $client;
    private $channel;
    private $user;
    private $identity;

    abstract public function configure();
    abstract public function execute($message, $context);

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
}
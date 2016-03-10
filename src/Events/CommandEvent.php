<?php
namespace Zilack\Events;

use Symfony\Component\EventDispatcher\Event;
use Zilack\ZilackCommand;

class CommandEvent extends Event
{
    /** @var  ZilackCommand $command */
    private $command;
    private $data;
    private $context;

    /**
     * @return ZilackCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param ZilackCommand $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

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
}
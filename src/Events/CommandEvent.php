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
    private $commandName;

    /**
     * CommandEvent constructor.
     *
     * @param $command
     * @param $data
     * @param $context
     * @param $commandName
     */
    public function __construct($command, $data, $context, $commandName)
    {
        $this->command = $command;
        $this->data = $data;
        $this->context = $context;
        $this->commandName = $commandName;
    }

    /**
     * @return ZilackCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getCommandName()
    {
        return $this->commandName;
    }
}
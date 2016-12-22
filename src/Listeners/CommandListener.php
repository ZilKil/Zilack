<?php
namespace Zilack\Listeners;

use Zilack\Events\CommandEvent;

class CommandListener
{
    public function onCommandReceived(CommandEvent $event)
    {
        $command = $event->getCommand();
        $data = $event->getData();
        $command->setChannel($data->channel);
        $command->setUser($data->user);
        $command->execute($data, $event->getContext(), $event->getCommandName());
    }
}

<?php
namespace Zilack\SampleCommands;

use Zilack\ZilackCommand;

class ExtendedResponseCommand extends ZilackCommand
{
    public function configure()
    {
        $this->setName('!response:extended');
    }

    public function execute($message, $context)
    {
        $this->getClient()->sendResponseAs(
            $this->getChannel(),
            $this->generateMessage($message),
            'Zilack The Great',
            'robot_face'
        );
    }

    private function generateMessage($message)
    {
        return sprintf("My name and icon was changed.\nYour request data was: `%s`", json_encode($message));
    }
}
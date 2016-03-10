<?php
namespace Zilack\SampleCommands;

use Zilack\ZilackCommand;

class PrivateResponseCommand extends ZilackCommand
{
    public function configure()
    {
        $this->setName('!response:private');
    }

    public function execute($message, $context)
    {
        $this->getClient()->sendResponse(
            $this->getUser(),
            $this->generateMessage($message)
        );
    }

    private function generateMessage($message)
    {
        return sprintf("Your request data was: `%s`", json_encode($message));
    }
}
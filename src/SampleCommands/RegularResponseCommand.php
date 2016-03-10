<?php
namespace Zilack\SampleCommands;

use Zilack\ZilackCommand;

class RegularResponseCommand extends ZilackCommand
{
    public function configure()
    {
        $this->setName('!response:regular');
    }

    public function execute($message, $context)
    {
        $this->getClient()->sendResponse(
            $this->getChannel(),
            $this->generateMessage($message)
        );
    }

    private function generateMessage($message)
    {
        return sprintf("Your request data was: `%s`", json_encode($message));
    }
}
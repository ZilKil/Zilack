<?php
namespace Zilack\SampleCommands;

use Zilack\ZilackCommand;

class IdentityResponseCommand extends ZilackCommand
{
    public function configure()
    {
        $this->setName('!response:identity');
        $this->setIdentity('primary');
    }

    public function execute($message, $context, $commandName)
    {
        $identity = $this->getIdentity();

        $this->getClient()->sendResponseAs(
            $this->getChannel(),
            $this->generateMessage($message),
            isset($identity['name']) ? $identity['name'] : null,
            isset($identity['icon']) ? $identity['icon'] : null,
            isset($identity['emoji']) ? $identity['emoji'] : null
        );
    }

    private function generateMessage($message)
    {
        return sprintf("Your request data was: `%s`", json_encode($message));
    }
}
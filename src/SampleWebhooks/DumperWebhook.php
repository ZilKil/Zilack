<?php
namespace Zilack\SampleWebhooks;

use Zilack\ZilackWebhook;

class DumperWebhook extends ZilackWebhook
{

    public function configure()
    {
        $this->setIdentity('burger');
        $this->setChannel('#general');
        $this->setEvent('webhook.received');
    }

    public function execute($message, $context)
    {
        $identity = $this->getIdentity();

        $this->getClient()->sendResponseAs(
            $this->getChannel(),
            $this->generateMessage($message),
            isset($identity['name']) ? $identity['name'] : null,
            isset($identity['icon']) ? $identity['icon'] : null
        );
    }

    private function generateMessage($message)
    {
        return sprintf("I just received a webhook. Here's the content: `%s`", json_encode($message));
    }
}
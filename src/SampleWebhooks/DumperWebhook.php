<?php
namespace Zilack\SampleWebhooks;

use React\Http\Request;
use Zilack\ZilackWebhook;

class DumperWebhook extends ZilackWebhook
{

    public function configure()
    {
        $this->setIdentity('burger');
        $this->setChannel('#general');
        $this->setEvent('webhook.received');
    }

    public function execute(Request $request, $context)
    {
        $identity = $this->getIdentity();

        $this->getClient()->sendResponseAs(
            $this->getChannel(),
            $this->generateMessage($request),
            isset($identity['name']) ? $identity['name'] : null,
            isset($identity['icon']) ? $identity['icon'] : null
        );
    }

    private function generateMessage(Request $request)
    {
        $data = $request->getPost();
        $data = empty($data) ? $request->getBody() : $data;
        return sprintf("I just received a webhook. Here's the content: `%s`", json_encode($data));
    }
}
<?php
namespace Zilack\Listeners;

use Zilack\Events\WebhookEvent;
use Zilack\ZilackWebhook;

class WebhookListener
{
    public function onWebhookReceived(WebhookEvent $event)
    {
        $webhook = $event->getWebhook();

        if (!is_null($webhook) && $this->validate($event->getPayload())) {
            $webhook->execute($event->getPayload(), $event->getContext());
        }
    }

    public function validate(array $payload)
    {
        return true;
    }
}

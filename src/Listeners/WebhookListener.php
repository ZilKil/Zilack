<?php
namespace Zilack\Listeners;

use Zilack\Events\WebhookEvent;

class WebhookListener
{
    public function onWebhookReceived(WebhookEvent $event)
    {
        $webhook = $event->getWebhook();

        if(!is_null($webhook)) {
            $data = $event->getData();
            $webhook->execute($data, $event->getContext());
        }
    }
}

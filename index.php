<?php

require 'vendor/autoload.php';

$bot = new \Zilack\ZilackBot();

$bot->setConfiguration(__DIR__.'/config/config.yml');

$bot
    ->register(new \Zilack\SampleCommands\ExtendedResponseCommand())
    ->register(new \Zilack\SampleCommands\RegularResponseCommand())
    ->register(new \Zilack\SampleCommands\PrivateResponseCommand())
    ->register(new \Zilack\SampleCommands\IdentityResponseCommand())
    ->register(new \Zilack\SampleWebhooks\DumperWebhook());
$bot->run();

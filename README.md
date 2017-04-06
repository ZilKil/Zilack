# Zilack - PHP Slack bot client

![GitHub version](https://badge.fury.io/gh/zilkil%2FZilack.svg)

## Introduction
Zilack is a feature rich lightweight Slack bot client written in PHP. The main purpose of it is to provide easy to use client with minimal configuration.

## Installation
Zilack is avalaible on [Packagist](https://packagist.org/packages/zilkil/zilack) and can be installed with [Composer](https://getcomposer.org/)

```console
composer require zilkil/zilack
```
That's pretty much it. Now you can use Zilack in your scripts.

## Usage
### Configuration
Zilack uses Yaml configuration file to get required settings. You need to load this file right after instansiating the bot. Here's an example of it:

```yaml
slack_settings:
  token: 'YOUR_SLACK_TOKEN'
zilack_settings:
  webhooks_enabled: 1
  webhooks_listener_host: 0.0.0.0
  webhooks_listener_port: 8081

  identities:
    primary:
      name: 'Primary Identity'
      icon: 'robot_face'
    burger:
      name: 'Burger Identity'
      icon: 'hamburger'
```

### Main script

In order to use Zilack's features, you need to load it into your scripts. Here's an example of `index.php` which instanciates the bot.

```php
<?php
require 'vendor/autoload.php';

//Instantiating the bot itself
$bot = new \Zilack\ZilackBot();

//Loading your configuration file
$bot->setConfiguration(__DIR__.'/config/config.yml');

//Registering bot commands and webhooks
$bot
    ->register(new \Zilack\SampleCommands\ExtendedResponseCommand())
    ->register(new \Zilack\SampleCommands\RegularResponseCommand())
    ->register(new \Zilack\SampleCommands\PrivateResponseCommand())
    ->register(new \Zilack\SampleCommands\IdentityResponseCommand())
    ->register(new \Zilack\SampleWebhooks\DumperWebhook());
    
//Starting the bot    
$bot->run();
```

## Components
Zilack currently supports two type of components - commands and incoming webhooks.
### Commands
Commands are triggered by specific keyword detected in user message in Slack chat. There is no naming restrictions on Zilack for commands, however, you need to make sure that you are able to type that keyword in chat. Recommended naming would be `!my:command`. Bellow is an example of `!hello` command which triggers bot response `Hello there!`

```php
class HelloCommand extends ZilackCommand
{
    public function configure()
    {
        $this->setName('!hello');
    }

    public function execute($message, $context, $commandName)
    {
        $this->getClient()->sendResponse($this->getChannel(), 'Hello there!');
    }
}
```

#### Sample Commands
There's a variety of sample commands in `\Zilack\SampleCommands` namespace. These commands are written to familiarize you with various possible response types and usage of different configurations.

|Command|Trigger|Description|
|-------|-------|-----------|
|RegularResponseCommand|`!response:regular`|Provides an example of regular response sent back to Slack|
|ExtendedResponseCommand|`!response:extended`|Provides an example of response with overwritten bot user name and icon without using identities configuration|
|IdentityResponseCommand|`!response:identity`|Provides an example of identities usage to overwrite default bot user name and icon|
|PrivateResponseCommand|`!response:private`|Provides an example of private response sent to user who triggered the command|

### Webhooks
Zilack has less control over incoming webhooks that over commands as they are too dynamic to handle them universally. Thus, incoming webhook events are triggered to all listeners and there must be a filter implementation to identify if webhook is correct.


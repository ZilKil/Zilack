<?php
namespace Zilack\Services;

use GuzzleHttp\Client;
use Zilack\Managers\ConfigManager;

class SlackClient extends Client
{
    private $token;
    /** @var  ConfigManager $configManager */
    private $configManager;

    public function sendResponseAs($channel, $message, $username, $icon = null, array $attachments = [])
    {
        $params = [
            'id' => time(),
            'token' => $this->token,
            'channel' => $channel,
            'text' => $message,
            'attachments' => json_encode($attachments)
        ];

        if(!is_null($username)) {
            $params['as_user'] = false;
            $params['username'] = $username;
        }

        if(!is_null($icon)) {
            $params['icon_emoji'] = ":{$icon}:";
        }

        $this->request('POST', $this->buildSlackUri($params, 'https://slack.com/api/chat.postMessage'));
    }

    public function sendResponse($channel, $message, $user = null, array $attachments = [])
    {
        $params = [
            'id' => time(),
            'token' => $this->token,
            'channel' => $channel,
            'text' => !is_null($user) ? "<@{$user}>: {$message}" : $message,
            'attachments' => json_encode($attachments)
        ];

        $this->request('POST', $this->buildSlackUri($params, 'https://slack.com/api/chat.postMessage'));
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function buildSlackUri($params, $uri)
    {
        return $uri . "?" . http_build_query($params);
    }

    public function setConfigManager($configManager)
    {
        $this->configManager = $configManager;
    }

    public function getConfigManager()
    {
        return $this->configManager;
    }
}
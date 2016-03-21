<?php
namespace Zilack\Services;

use GuzzleHttp\Client;
use Zilack\Managers\ConfigManager;
use Zilack\ZilackRegistry;

class SlackClient extends Client
{
    private $token;

    public function sendResponseAs($channel, $message, $username, $icon = null, $emoji = null, array $attachments = [])
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
            $params['icon_url'] = $icon;
        }
        elseif(!is_null($emoji)) {
            $params['icon_emoji'] = ":{$emoji}:";
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

    /**
     * @return ConfigManager
     */
    public function getConfigManager()
    {
        return ZilackRegistry::get('configManager');
    }
}
<?php
namespace Zilack\Managers;

use Symfony\Component\Yaml\Parser;

class ConfigManager
{
    const SETTINGS_ROOT = 'zilack_settings';

    private $config;
    /** @var Parser $parser */
    private $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        if(!file_exists($config) || !is_readable($config)) {
            throw new \Exception('Unable to parse configuration file.');
        }

        $this->config = $this->parser->parse(file_get_contents($config));
    }

    public function getConfigParam($param)
    {
        return $this->config[$param];
    }

    public function getIdentity($identity)
    {
        $settings = $this->getConfigParam(self::SETTINGS_ROOT);

        if((isset($settings['identities'][$identity]))) {
            return $settings['identities'][$identity];
        }

        return null;
    }

    public function getZilackConfig()
    {
        return $this->getConfigParam(self::SETTINGS_ROOT);
    }
}
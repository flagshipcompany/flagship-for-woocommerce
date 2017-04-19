<?php

namespace FS\Context;

use FS\Container;
use FS\Components\Factory\ConfigurationInterface;
use FS\Components\Options;

class ApplicationContext extends AbstractApplicationContext
{
    public static $instance;

    public function debug($data)
    {
        $this->_('\\FS\\Components\\Debugger')->log($data);
    }

    public function alert($message, $type = 'notice')
    {
        $notifier = $this->getComponent('\\FS\\Components\\Alert\\Notifier');

        $notifier->add($type, $message);

        return $this;
    }

    public function setting($key)
    {
        $settings = $this->getComponent('\\FS\\Components\\Settings');

        return $settings[$key];
    }

    public function factory($factory)
    {
        $factory = $this->getComponent($factory);

        return $factory;
    }

    public function api(Options $option = null)
    {
        $client = $this->getComponent('\\FS\\Components\\Http\\Client');

        if ($option) {
            $client->setToken($option->get('token'));
        }

        return $client;
    }

    public function option($key = null)
    {
        $options = $this->getComponent('\\FS\\Components\\Options');

        if (!$key) {
            return $options;
        }

        return $options->get($key);
    }

    public function command()
    {
        return $this->getComponent('\\FS\\Components\\Shipping\\Command');
    }

    public static function initialize(Container $container, ConfigurationInterface $configurator)
    {
        $ctx = self::getInstance();

        $ctx->setContainer($container);
        $ctx->setConfiguration($configurator);

        $ctx->configure($configurator);

        return $ctx;
    }

    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

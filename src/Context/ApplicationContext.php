<?php

namespace FS\Context;

use FS\Container;
use FS\Components\Factory\ConfigurationInterface;

class ApplicationContext extends AbstractApplicationContext
{
    public static $instance;

    public function debug($data)
    {
        $this->_('\\FS\\Components\\Debugger')->log($data);
    }

    public function alert($message, $type = 'notice')
    {
        $notifier = $this->getComponent('\\FS\\Components\\Notifier');

        $notifier->add($type, $message);

        return $this;
    }

    public function setting($key)
    {
        $settings = $this->getComponent('\\FS\\Components\\Settings');

        return $settings[$key];
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

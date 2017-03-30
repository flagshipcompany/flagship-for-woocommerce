<?php

namespace FS\Context;

use FS\Container;
use FS\Components\Factory\ComponentFactoryInterface;
use FS\Components\Factory\ConfigurationInterface;

class ApplicationContext extends AbstractApplicationContext implements ConfigurableApplicationContextInterface, ComponentFactoryInterface
{
    public static $instance;

    public function debug($data)
    {
        $this->_('\\FS\\Components\\Debugger')->log($data);
    }

    public static function initialize(Container $container, ConfigurationInterface $configuration)
    {
        $ctx = self::getInstance();

        $ctx->setContainer($container);
        $ctx->setConfiguration($configuration);

        foreach ([
            '\\FS\\Components\\Web\\RequestParam',
            '\\FS\\Components\\Settings',
            '\\FS\\Components\\Options',
            '\\FS\\Components\\Debugger',
            '\\FS\\Components\\Html',
            '\\FS\\Components\\Viewer',
            '\\FS\\Components\\Url',
            '\\FS\\Components\\Notifier',
            '\\FS\\Components\\Event\\ApplicationEventCaster',
            '\\FS\\Components\\Event\\Factory\\ApplicationListenerFactory',
            '\\FS\\Components\\Http\\Client',
        ] as $class) {
            $ctx->_($class);
        }

        $ctx->_('\\FS\\Components\\Event\\Factory\\ApplicationListenerFactory')
            ->addApplicationListeners($ctx);

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

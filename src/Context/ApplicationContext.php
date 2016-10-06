<?php

namespace FS\Context;

class ApplicationContext extends AbstractApplicationContext implements ConfigurableApplicationContextInterface,\FS\Components\Factory\ComponentFactoryInterface
{
    public static $instance;
    public $text_domain = FLAGSHIP_SHIPPING_TEXT_DOMAIN;

    public function getComponents(array $classes)
    {
        foreach ($classes as $class) {
            $this->getComponent($class);
        }

        return $this;
    }

    public function debug($data)
    {
        $this->getComponent('\\FS\\Components\\Debugger')->log($data);
    }

    public static function initialize(\FS\Container $container, \FS\Components\Factory\ConfigurationInterface $configuration)
    {
        $ctx = self::getInstance();

        $ctx->setContainer($container);
        $ctx->setConfiguration($configuration);

        $ctx->getComponents(array(
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
            '\\FS\\Components\\Hook\\HookManager',
            '\\FS\\Components\\Http\\Client',
        ));

        $ctx->getComponent('\\FS\\Components\\Event\\Factory\\ApplicationListenerFactory')
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

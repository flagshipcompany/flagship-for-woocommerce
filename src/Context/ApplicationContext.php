<?php

namespace FS\Context;

class ApplicationContext extends AbstractApplicationContext implements \FS\Components\Factory\ComponentFactoryInterface
{
    public static $instance;

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
            '\\FS\\Components\\Hook\\HookManager',
            '\\FS\\Components\\Http\\Client',
        ));

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

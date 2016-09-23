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

    public static function initialize(\FS\Container $container, \FS\Configuration $configuration)
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

    public static function init($configs = array())
    {
        $ctx = self::getInstance();

        $ctx->getComponent('\\FS\\Components\\Configs\\Configuration');

        spl_autoload_register(array('FSApplicationContext', 'autoload'));

        $ctx->load('Configs');
        $ctx['configs']->add($configs);
        if ($ctx['configs']->get('FLAGSHIP_SHIPPING_PLUGIN_DEBUG')) {
            $ctx['configs']->add(array(
                'FLAGSHIP_SHIPPING_API_ENTRY_POINT' => 'http://127.0.0.1:3002',
            ));

            $ctx->load('Console');
        }

        $ctx->dependency(array(
            'Request', //
            'Html',
            'View',
            'Options', //
            'Client',
            'Notification',
            'Validation',
            'Hook',
            'Url',
            'Address',
        ));

        $component = new FSComponent($ctx);

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

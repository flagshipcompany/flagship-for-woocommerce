<?php

namespace FS;

use FS\Injection\I;
use FS\Injection\Http\Client as HttpClient;
use FS\Components\Factory\ConfigurationInterface;
use FS\Components\Event\Listener;
use FS\Configurations\WordPress;
use FS\Context\ConfigurableApplicationContextInterface as Context;

class Configurator implements ConfigurationInterface
{
    /**
     * configure application context.
     *
     * @param Context $ctx
     */
    public function configure(Context $ctx)
    {
        // initialize singletons
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

        // below 3 statements are equivalent

        // 1.
        // foreach ([
        //     '\\FS\\Components\\Web\\RequestParam',
        // ] as $class) {
        //     $ctx->_($class);
        // }

        // 2.
        // $ctx->_('\\FS\\Components\\Web\\RequestParam');

        // 3.
        // $ctx['\\FS\\Components\\Web\\RequestParam'] = (new \FS\Components\Web\RequestParam())->setApplicationContext($ctx);

        // register events
        $ctx->_('\\FS\\Components\\Event\\Factory\\ApplicationListenerFactory')
            ->addApplicationListeners([
                // normal
                new Listener\PluginInitialization(),
                new Listener\MetaboxOperations(),
                new Listener\MetaboxDisplay(),
                new Listener\ShippingMethodSetup(),
                new Listener\ShippingZoneMethodOptions(),
                new Listener\CalculateShipping(),
                // admin
                new Listener\PluginPageSettingLink(),
                new Listener\PickupPostType(),
                new Listener\ShippingZoneMethodAdd(),
            ]);
    }

    public function getOptions()
    {
        $options = new \FS\Components\Options();
        $options->setWpOptionName('woocommerce_flagship_shipping_method_settings');

        return $options;
    }

    public function getSettings()
    {
        $settings = new \FS\Components\Settings();

        $settings['FLAGSHIP_SHIPPING_PLUGIN_DEBUG'] = I::isDebugMode();
        $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'] = 'flagship_shipping_method';
        $settings['FLAGSHIP_SHIPPING_API_ENTRY_POINT'] = $settings['FLAGSHIP_SHIPPING_PLUGIN_DEBUG'] ? 'http://127.0.0.1:3002' : 'https://api.smartship.io';
        $settings['FLAGSHIP_SHIPPING_API_TIMEOUT'] = 14;
        $settings['FLAGSHIP_FOR_WOOCOMMERCE_VERSION'] = I::version();

        return $settings;
    }

    public function getDebugger()
    {
        return new \FS\Components\Debugger();
    }

    public function getRequestParam()
    {
        return new \FS\Components\Web\RequestParam();
    }

    public function getNotifier()
    {
        $notifier = new \FS\Components\Notifier();

        $notifier->setViewer($this->getViewer());

        return $notifier;
    }

    public function getViewer()
    {
        return new \FS\Components\Viewer();
    }

    public function getHtml()
    {
        return new \FS\Components\Html();
    }

    public function getUrl()
    {
        return new \FS\Components\Url();
    }

    public function getHookManager()
    {
        $manager = new \FS\Components\Hook\HookManager();

        return $manager;
    }

    public function getClient()
    {
        $client = new \FS\Components\Http\Client();

        $settings = $this->getSettings();

        $client->withOptions([
            'timeout' => $settings['FLAGSHIP_SHIPPING_API_TIMEOUT'],
            'base' => $settings['FLAGSHIP_SHIPPING_API_ENTRY_POINT'],
            'runner' => new HttpClient(),
        ]);

        return $client;
    }

    public function getShopFactory()
    {
        $factory = new \FS\Components\Shop\Factory\ShopFactory();
        $factory->setFactoryDriver(new WordPress\Shop\Factory\Driver());

        return $factory;
    }

    public function getViewFactory()
    {
        $factory = new \FS\Components\View\Factory\ViewFactory();
        $factory->setFactoryDriver(new WordPress\View\Factory\Driver());

        return $factory;
    }

    public function getRateProcessor()
    {
        return new \FS\Components\Shipping\RateProcessor();
    }
}

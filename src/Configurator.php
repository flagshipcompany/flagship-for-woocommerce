<?php

namespace FS;

use FS\Injection\I;
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

        $client->setTimeout($settings['FLAGSHIP_SHIPPING_API_TIMEOUT']);
        $client->setEntryPoint($settings['FLAGSHIP_SHIPPING_API_ENTRY_POINT']);
        $client->setRequestRunner($this->getRequestRunner());

        return $client;
    }

    public function getRequestRunner()
    {
        $requestRunner = new \FS\Components\Http\RequestRunner\RequestRunner();
        $requestRunner->setRequestRunnerDriver(new \FS\Configurations\WordPress\Http\RequestRunner\Driver());

        return $requestRunner;
    }

    public function getRequestBuilderFactory()
    {
        $factory = new \FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory();
        $factory->setFactoryDriver(new WordPress\RequestBuilder\Factory\Driver());

        return $factory;
    }

    public function getRateProcessorFactory()
    {
        $factory = new \FS\Components\Shipping\RateProcessor\Factory\RateProcessorFactory();
        $factory->setFactoryDriver(new WordPress\RateProcessor\Factory\Driver());

        return $factory;
    }

    public function getValidatorFactory()
    {
        $factory = new \FS\Components\Validation\Factory\ValidatorFactory();
        $factory->setFactoryDriver(new WordPress\Validation\Factory\Driver());

        return $factory;
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

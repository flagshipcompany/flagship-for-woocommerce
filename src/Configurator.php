<?php

namespace FS;

use FS\Injection\I;
use FS\Injection\Http\Client as HttpClient;
use FS\Components\Factory\ConfigurationInterface;
use FS\Components\Event\Listener;
use FS\Context\ConfigurableApplicationContextInterface as Context;

class Configurator implements ConfigurationInterface
{
    /**
     * configure application context.
     *
     * @param Context $ctx
     */
    public function configure(Context $context)
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
            $context->_($class);
        }

        // register events (WordPress hooks)
        $context->_('\\FS\\Components\\Event\\Factory\\ApplicationListenerFactory')
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

        $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'] = 'flagship_shipping_method';
        $settings['FLAGSHIP_SHIPPING_API_ENTRY_POINT'] = I::isDebugMode() ? 'http://127.0.0.1:3002' : 'https://api.smartship.io';
        $settings['FLAGSHIP_SHIPPING_API_TIMEOUT'] = 14;
        $settings['FLAGSHIP_FOR_WOOCOMMERCE_VERSION'] = I::version();

        return $settings;
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
}

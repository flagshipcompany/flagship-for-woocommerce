<?php

namespace FS\Components\Event\Listener;

use FS\Injection\I;

class ShippingMethodSetup extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface, \FS\Configurations\WordPress\Event\NativeHookInterface
{
    public function getSupportedEvent()
    {
        return 'FS\\Configurations\\WordPress\\Event\\ShippingMethodSetupEvent';
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
        $methods = $event->getInputs();

        $settings = $context->getComponent('\\FS\\Components\\Settings');
        $id = $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'];

        if (\version_compare(WC()->version, '2.6', '>=')) {
            $methods[$id] = '\\FS\\Configurations\\WordPress\\Shipping\\Method\\FlagShipWcShippingMethod';
        } else {
            include_once I::directory('PLUGIN').'src/Configurations/WordPress/Shipping/Method/Legacy_Flagship_WC_Shipping_Method.php';

            $methods[$id] = new \FlagShip_WC_Shipping_Method();
        }

        return $methods;
    }

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        \add_filter('woocommerce_shipping_methods', function ($methods) use ($context) {
            $event = new \FS\Configurations\WordPress\Event\ShippingMethodSetupEvent();
            $event->setInputs($methods);

            return $context->publishEvent($event);
        });
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}

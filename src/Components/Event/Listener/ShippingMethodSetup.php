<?php

namespace FS\Components\Event\Listener;

use FS\Injection\I;
use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class ShippingMethodSetup extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::SHIPPING_METHOD_SETUP;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $methods = $event->getInputs();

        $settings = $context->_('\\FS\\Components\\Settings');
        $id = $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'];

        if (\version_compare(WC()->version, '2.6', '>=')) {
            $methods[$id] = '\\FS\\Components\Shipping\\Method\\FlagShipWcShippingMethod';
        } else {
            include_once I::directory('PLUGIN').'src/Configurations/WordPress/Shipping/Method/Legacy_Flagship_WC_Shipping_Method.php';

            $methods[$id] = new \FlagShip_WC_Shipping_Method();
        }

        return $methods;
    }

    public function publishNativeHook(Context $context)
    {
        \add_filter('woocommerce_shipping_methods', function ($methods) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::SHIPPING_METHOD_SETUP);
            $event->setInputs($methods);

            return $context->publishEvent($event);
        });
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}

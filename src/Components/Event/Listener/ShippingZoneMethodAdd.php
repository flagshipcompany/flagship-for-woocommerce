<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class ShippingZoneMethodAdd extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::SHIPPING_ZONE_METHOD_ADD;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $instanceId = $event->getInput('instanceId');

        $options = $context->_('\\FS\\Components\\Options');
        $settings = $context->_('\\FS\\Components\\Settings');

        \update_option('woocommerce_'.$settings['FLAGSHIP_SHIPPING_PLUGIN_ID'].'_'.$instanceId.'_settings', $options->all());
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('woocommerce_shipping_zone_method_added', function ($instanceId, $type, $zoneId) use ($context) {
            $settings = $context->_('\\FS\\Components\\Settings');
            $options = $context->_('\\FS\\Components\\Options');

            if ($type == $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'] && $options->all()) {
                $event = new ApplicationEvent(ApplicationEvent::SHIPPING_ZONE_METHOD_ADD);

                $event->setInputs(array(
                    'instanceId' => $instanceId,
                ));

                $context->publishEvent($event);
            }
        }, 10, 3);
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}

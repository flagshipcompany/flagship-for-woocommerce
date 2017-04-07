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

        \update_option('woocommerce_'.$context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID').'_'.$instanceId.'_settings', $context->option()->all());
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('woocommerce_shipping_zone_method_added', function ($instanceId, $type, $zoneId) use ($context) {
            if ($type == $context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID') && $context->option()->all()) {
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

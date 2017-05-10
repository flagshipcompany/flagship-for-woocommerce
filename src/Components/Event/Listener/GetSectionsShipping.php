<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class GetSectionsShipping extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::GET_SECTIONS_SHIPPING;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        //Since on woocommerce3.0.x, settings are for each shipping zone, the global settings for FlagShip method will not be displayed.
        $sections = $event->getInput('sections');
        unset($sections[$context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID')]);

        return $sections;
    }

    public function publishNativeHook(Context $context)
    {
        \add_filter('woocommerce_get_sections_shipping', function ($sections) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::GET_SECTIONS_SHIPPING);
            $event->setInputs(array(
                'sections' => $sections,
            ));

            return $context->publishEvent($event);
        }, 10, 1);
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}

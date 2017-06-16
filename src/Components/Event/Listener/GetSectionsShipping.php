<?php

namespace FS\Components\Event\Listener;

use FS\Injection\I;
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
        $sections = $event->getInput('sections');
        unset($sections['flagship_shipping_method']);

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

<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class SignatureRequiredInCheckout extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::SIGNATURE_REQUIRED_IN_CHECKOUT;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $fields = $event->getInput('fields');

        if (isset($fields['order']) && $context->getGeneralSetting('enable_signature_required') === 'yes') {
            $fields['order']['signature_required'] = array(
                    'type' => 'checkbox',
                    'label' => __('Signature required on delivery', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                );
        }

        return $fields;
    }

    public function publishNativeHook(Context $context)
    {
        \add_filter('woocommerce_checkout_fields', function ($fields) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::SIGNATURE_REQUIRED_IN_CHECKOUT);
            $event->setInputs(array(
                'fields' => $fields,
            ));

            return $context->publishEvent($event);
        }, 10, 1);
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}

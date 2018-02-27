<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;
use FS\Injection\I;

class UpdateSignatureRequiredInOrder extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::UPDATE_SIGNATURE_IN_ORDER;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $orderId = $event->getInput('order_id');
        $data = $event->getInput('data');

        if (!empty($data['signature_required'])) {
            \update_post_meta($orderId, 'flagship_signature_required', 'yes');
        }
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('woocommerce_checkout_update_order_meta', function ($orderId, $data) use ($context) {
            I::__($data);
            $event = new ApplicationEvent(ApplicationEvent::UPDATE_SIGNATURE_IN_ORDER);
            $event->setInputs(array(
                'order_id' => $orderId,
                'data' => $data,
            ));

            return $context->publishEvent($event);

        }, 10, 2);
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}

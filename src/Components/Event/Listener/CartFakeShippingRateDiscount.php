<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class CartFakeShippingRateDiscount extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::CART_FAKE_SHIPPING_RATE_DISCOUNT;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $label = $event->getInput('label');
        $cost = $event->getInput('cost');

        if ($context->option()->eq('allow_fake_cart_rate_discount', 'yes') && floatval($cost) != 0) {
            $label .= '&nbsp;<del style="color:red;">'.wc_price($cost * (1 + $context->option('fake_cart_rate_discount') / 100)).'</del>';
        }

        return $label;
    }

    public function publishNativeHook(Context $context)
    {
        \add_filter('woocommerce_cart_shipping_method_full_label', function ($label, $method) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::CART_FAKE_SHIPPING_RATE_DISCOUNT);
            $event->setInputs(array(
                'label' => $label,
                'cost' => $method->cost,
            ));

            return $context->publishEvent($event);
        }, 10, 2);
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}

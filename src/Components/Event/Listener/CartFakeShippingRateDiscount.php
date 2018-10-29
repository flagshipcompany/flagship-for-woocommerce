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
        $method = $event->getInput('method');
        $methodId = explode('|', $method->id)[0];
        $cost = $method->cost;
        preg_match('/discount_rate=(\d)+/', $label, $matches);

        if ($matches) {
            $label = preg_replace('/discount_rate=(\d)+/', '', $label);
        }

        if ((floatval($cost) != 0)
            && ($context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID') == $methodId) && $matches) {
            $discountRate = explode('=', $matches[0])[1];
            $label .= '&nbsp;<del style="color:red;">'.wc_price($cost * (1 +  $discountRate/ 100)).'</del>';
        }

        return $label;
    }

    public function publishNativeHook(Context $context)
    {
        \add_filter('woocommerce_cart_shipping_method_full_label', function ($label, $method) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::CART_FAKE_SHIPPING_RATE_DISCOUNT);
            $event->setInputs(array(
                'label' => $label,
                'method' => $method,
            ));

            return $context->publishEvent($event);
        }, 10, 2);
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}

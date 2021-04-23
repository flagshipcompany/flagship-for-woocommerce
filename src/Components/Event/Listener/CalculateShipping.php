<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;
use FS\Components\Event\NativeHookInterface;
use FS\Components\Alert\Notifier;

class CalculateShipping extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    protected $flagshipShippingRates;

    public function getSupportedEvent()
    {
        return ApplicationEvent::CALCULATE_SHIPPING;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $package = $event->getInput('package');
        $method = $event->getInput('method');

        $context
            ->controller('\\FS\\Components\\Shipping\\Controller\\ShippingController', [
                'compute' => 'calculate',
            ])
            ->before(function ($context) {
                // apply middlware function before invoke controller method
                $option = $context->option();

                $context->api($option);

                $notifier = $context->alert(Notifier::SCOPE_CART);

                // when store owner disable front end warning for their customer
                if ($option->eq('disable_api_warning', 'yes')) {
                    $notifier->getScenario()->enableSilentLogging();
                }
            })
            ->after(function ($context) {
                // we have to explicit "show" notification
                $context->alert()->view();
            })
            ->dispatch('compute', [$package, $method]);
    }

    public function publishNativeHook(Context $context)
    {
        $id = $context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID');

        // Solve conflicts with B2B plugin
        if (empty(get_option('afb2b_shipping'))) {
            return;
        }

        \add_filter('woocommerce_package_rates', function ($shipping_methods, $package) use ($id) {
            array_walk($shipping_methods, function($value, $key) use ($id) {
                if (false !== strpos($key, $id)) {
                    $this->flagshipShippingRates[$key] = $value->get_method_id();
                    $value->set_method_id($id);
                }
            });

            return $shipping_methods;
        }, 99, 2);

        \add_filter('woocommerce_package_rates', function ($shipping_methods, $package) use ($id) {
            array_walk($shipping_methods, function($value, $key) use ($id) {
                if (in_array($key, $this->flagshipShippingRates)) {
                    $value->set_method_id($key);
                }
            });

            return $shipping_methods;
        }, 101, 2);
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}

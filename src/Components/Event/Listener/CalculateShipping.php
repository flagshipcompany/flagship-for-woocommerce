<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class CalculateShipping extends AbstractComponent implements ApplicationListenerInterface
{
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

                $context
                    ->api()
                    ->setToken($option->get('token'));

                $notifier = $context
                    ->_('\\FS\\Components\\Notifier')
                    ->scope('cart');

                // when store owner disable front end warning for their customer
                if ($option->eq('disable_api_warning', 'yes')) {
                    $notifier->enableSilentLogging();
                }
            })
            ->after(function ($context) {
                // we have to explicit "show" notification
                $context
                    ->_('\\FS\\Components\\Notifier')
                    ->view();
            })
            ->dispatch('compute', [$package, $method]);
    }
}

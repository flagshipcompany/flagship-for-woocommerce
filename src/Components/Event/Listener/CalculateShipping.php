<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;
use FS\Components\Alert\Notifier;

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

                $context->api($option);

                $notifier = $context
                    ->_('\\FS\\Components\\Alert\\Notifier')
                    ->scenario(Notifier::SCOPE_CART);

                // when store owner disable front end warning for their customer
                if ($option->eq('disable_api_warning', 'yes')) {
                    $notifier->getScenario()->enableSilentLogging();
                }
            })
            ->after(function ($context) {
                // we have to explicit "show" notification
                $context
                    ->_('\\FS\\Components\\Alert\\Notifier')
                    ->view();
            })
            ->dispatch('compute', [$package, $method]);
    }
}

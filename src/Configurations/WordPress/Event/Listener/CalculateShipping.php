<?php

namespace FS\Configurations\WordPress\Event\Listener;

class CalculateShipping extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface
{
    public function getSupportedEvent()
    {
        return 'FS\\Configurations\\WordPress\\Event\\CalculateShippingEvent';
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
        $package = $event->getInput('package');
        $method = $event->getInput('method');

        $options = $context
            ->getComponent('\\FS\\Components\\Options');
        $command = $context
            ->getComponent('\\FS\\Components\\Shipping\\Command');
        $factory = $context
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingCartRateRequestFactory');
        $client = $context
            ->getComponent('\\FS\\Components\\Http\\Client')
            ->setToken($options->get('token'));
        $notifier = $context
            ->getComponent('\\FS\\Components\\Notifier')
            ->scope('cart');
        $rateProcessor = $context
            ->getComponent('\\FS\\Components\\Shipping\\RateProcessor');

        // when store owner disable front end warning for their customer
        if ($options->equal('disable_api_warning', 'yes')) {
            $notifier->enableSilentLogging();
        }

        // no shipping address, alert customer
        if (empty($package['destination']['postcode'])) {
            $notifier->notice('Add shipping address to get shipping rates! (click "Calculate Shipping")');
            $notifier->view();

            return;
        }

        $response = $command->quote(
            $client,
            $factory->setPayload(array(
                'package' => $package,
                'options' => $options,
                'notifier' => $notifier,
            ))->getRequest()
        );

        if (!$response->isSuccessful()) {
            $notifier->error('Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>');
            $notifier->view();

            return;
        }

        $rates = $rateProcessor->convertToWcShippingRate($response->getBody(), $this->instance_id);

        $offer_rates = $method->get_instance_option('offer_rates', 'all');

        if ($offer_rates == 'all') {
            foreach ($rates as $rate) {
                $method->add_rate($rate);
            }

            return;
        }

        if ($offer_rates == 'cheapest') {
            $method->add_rate($rates[0]);

            return;
        }

        $count = intval($offer_rates);

        while ($count > 0 && $rates) {
            $rate = array_shift($rates);
            $method->add_rate($rate);

            --$count;
        }

        $notifier->view();
    }
}

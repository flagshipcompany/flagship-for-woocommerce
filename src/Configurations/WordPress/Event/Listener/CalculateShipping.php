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

        $settings = $context->getComponent('\\FS\\Components\\Settings');
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
        $rateProcessorFactory = $context
            ->getComponent('\\FS\\Components\\Shipping\\RateProcessor\\Factory\\RateProcessorFactory');

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

        $rates = $response->getBody();

        // filter and process on returned rates
        $rates = $rateProcessorFactory
            ->getRateProcessor('EnabledRate')
            ->getProcessedRates($rates, array(
                'enabled' => array(
                    'standard' => ($options->get('allow_standard_rates') == 'yes'),
                    'express' => ($options->get('allow_express_rates') == 'yes'),
                    'overnight' => ($options->get('allow_overnight_rates') == 'yes'),
                ),
            ));

        $rates = $rateProcessorFactory
            ->getRateProcessor('CourierExcludedRate')
            ->getProcessedRates($rates, array(
                'excluded' => array_filter(array('fedex', 'ups', 'purolator'), function ($courier) use ($options) {
                    return $options->not_equal('disable_courier_'.$courier, 'no');
                }),
            ));

        $rates = $rateProcessorFactory
            ->getRateProcessor('XNumberOfBestRate')
            ->getProcessedRates($rates, array(
                'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
                'offered' => $method->get_instance_option('offer_rates', 'all'),
            ));

        $rates = $rateProcessorFactory
            ->getRateProcessor('NativeRate')
            ->getProcessedRates($rates, array(
                'settings' => $settings,
                'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
                'markup' =>  array(
                    'type' => $options->get('default_shipping_markup_type'),
                    'rate' => $options->get('default_shipping_markup'),
                ),
                'instanceId' => property_exists($method, 'instance_id') ? $method->instance_id : false,
            ));

        foreach ($rates as $rate) {
            $method->add_rate($rate);
        }

        $notifier->view();
    }
}

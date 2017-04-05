<?php

namespace FS\Components\Shipping\Controller;

use FS\Components\AbstractComponent;
use FS\Components\Web\RequestParam as Req;
use FS\Context\ApplicationContext as App;

class ShippingController extends AbstractComponent
{
    public function calculate(Req $request, App $context, $package, $method)
    {
        $options = $context
            ->_('\\FS\\Components\\Options');
        $command = $context
            ->_('\\FS\\Components\\Shipping\\Command');
        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Factory\\ShoppingCartRateRequestFactory');
        $client = $context
            ->_('\\FS\\Components\\Http\\Client');
        $notifier = $context
            ->_('\\FS\\Components\\Notifier');

        $rateProcessorFactory = $context
            ->_('\\FS\\Components\\Shipping\\RateProcessor\\Factory\\RateProcessorFactory');

        // no shipping address, alert customer
        if (empty($package['destination']['postcode'])) {
            $context->alert('Add shipping address to get shipping rates! (click "Calculate Shipping")', 'notice');

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
            $context->alert('Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>', 'error');

            return;
        }

        $rates = $response->getContent();

        $rates = $rateProcessorFactory
            ->getRateProcessor('ProcessRate')
            ->getProcessedRates($rates, array(
                'factory' => $rateProcessorFactory,
                'options' => $options,
                'instanceId' => property_exists($method, 'instance_id') ? $method->instance_id : false,
                'methodId' => $context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID'),
            ));

        foreach ($rates as $rate) {
            $method->add_rate($rate);
        }
    }
}

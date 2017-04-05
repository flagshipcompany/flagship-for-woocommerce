<?php

namespace FS\Components\Shipping\RateProcessor;

use FS\Components\AbstractComponent;

class ProcessRateProcessor extends AbstractComponent implements RateProcessorInterface
{
    public function getProcessedRates($rates, $payload = [])
    {
        $options = $payload['options'];
        $instanceId = $payload['instanceId'];
        $factory = $payload['factory'];
        $methodId = $payload['methodId'];

        $rates = $factory
            ->resolve('EnabledRate')
            ->getProcessedRates($rates, [
                'enabled' => [
                    'standard' => ($options->get('allow_standard_rates') == 'yes'),
                    'express' => ($options->get('allow_express_rates') == 'yes'),
                    'overnight' => ($options->get('allow_overnight_rates') == 'yes'),
                ],
            ]);

        $rates = $factory
            ->resolve('CourierExcludedRate')
            ->getProcessedRates($rates, [
                'excluded' => array_filter(['fedex', 'ups', 'purolator'], function ($courier) use ($options) {
                    return $options->neq('disable_courier_'.$courier, 'no');
                }),
            ]);

        $rates = $factory
            ->resolve('XNumberOfBestRate')
            ->getProcessedRates($rates, [
                'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
                'offered' => $options->get('offer_rates', 'all'),
            ]);

        $rates = $factory
            ->resolve('NativeRate')
            ->getProcessedRates($rates, [
                'methodId' => $methodId,
                'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
                'markup' => array(
                    'type' => $options->get('default_shipping_markup_type'),
                    'rate' => $options->get('default_shipping_markup'),
                ),
                'instanceId' => $instanceId,
            ]);

        return $rates;
    }
}

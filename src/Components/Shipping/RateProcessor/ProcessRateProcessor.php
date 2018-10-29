<?php

namespace FS\Components\Shipping\RateProcessor;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\Object\Courier;

class ProcessRateProcessor extends AbstractComponent implements RateProcessorInterface
{
    public function getProcessedRates($rates, $payload = [])
    {
        $options = $payload['options'];
        $instanceId = $payload['instanceId'];
        $factory = $payload['factory'];
        $methodId = $payload['methodId'];

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
                'showTransitTime' => ($options->get('show_transit_time') == 'yes'),
            ]);

        $rates = $factory
            ->resolve('EnabledRate')
            ->getProcessedRates($rates, [
                'enabled' => [
                    'standard' => is_null($options->get('allow_standard_rates')) || ($options->get('allow_standard_rates') == 'yes'),
                    'express' => is_null($options->get('allow_express_rates')) || ($options->get('allow_express_rates') == 'yes'),
                    'overnight' => is_null($options->get('allow_overnight_rates')) || ($options->get('allow_overnight_rates') == 'yes'),
                ],
            ]);

        $rates = $factory
            ->resolve('CourierExcludedRate')
            ->getProcessedRates($rates, [
                'excluded' => array_filter(Courier::$couriers, function ($courier) use ($options) {
                    return !is_null($options->get('disable_courier_'.$courier)) && $options->neq('disable_courier_'.$courier, 'no');
                }),
            ]);

        $rates = $factory
            ->resolve('XNumberOfBestRate')
            ->getProcessedRates($rates, [
                'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
                'offered' => is_null($options->get('offer_rates')) || $options->get('offer_rates', 'all'),
            ]);


        return $rates;
    }
}

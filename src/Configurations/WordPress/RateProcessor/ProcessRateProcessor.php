<?php

namespace FS\Configurations\WordPress\RateProcessor;

class ProcessRateProcessor extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RateProcessor\RateProcessorInterface
{
    public function getProcessedRates($rates, $payload = array())
    {
        $options = $payload['options'];
        $instanceId = $payload['instanceId'];
        $factory = $payload['factory'];
        $methodId = $payload['methodId'];

        $rates = $factory
            ->getRateProcessor('EnabledRate')
            ->getProcessedRates($rates, array(
                'enabled' => array(
                    'standard' => ($options->get('allow_standard_rates') == 'yes'),
                    'express' => ($options->get('allow_express_rates') == 'yes'),
                    'overnight' => ($options->get('allow_overnight_rates') == 'yes'),
                ),
            ));

        $rates = $factory
            ->getRateProcessor('CourierExcludedRate')
            ->getProcessedRates($rates, array(
                'excluded' => array_filter(array('fedex', 'ups', 'purolator'), function ($courier) use ($options) {
                    return $options->neq('disable_courier_'.$courier, 'no');
                }),
            ));

        $rates = $factory
            ->getRateProcessor('XNumberOfBestRate')
            ->getProcessedRates($rates, array(
                'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
                'offered' => $options->get('offer_rates', 'all'),
            ));

        $rates = $factory
            ->getRateProcessor('NativeRate')
            ->getProcessedRates($rates, array(
                'methodId' => $methodId,
                'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
                'markup' => array(
                    'type' => $options->get('default_shipping_markup_type'),
                    'rate' => $options->get('default_shipping_markup'),
                ),
                'instanceId' => $instanceId,
            ));

        return $rates;
    }
}

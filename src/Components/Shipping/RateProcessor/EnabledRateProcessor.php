<?php

namespace FS\Components\Shipping\RateProcessor;

use FS\Components\AbstractComponent;

class EnabledRateProcessor extends AbstractComponent implements RateProcessorInterface
{
    public static $mapping = array(
        //  => standard
        'standard' => 'standard',
        'intlStandard' => 'standard',
        //  => 'express'
        'express' => 'express',
        'secondDay' => 'express',
        'thirdDay' => 'express',
        'intlExpress' => 'express',
        //  => 'overnight'
        'overnight' => 'overnight',
        'expressAm' => 'overnight',
        'expressEarlyAm' => 'overnight',
        'intlExpressAm' => 'overnight',
        'intlExpressEarlyAm' => 'overnight',
    );

    public function getProcessedRates($rates, $payload = [])
    {
        $enabled = $payload['enabled'];

        if ($enabled['standard'] && $enabled['express'] && $enabled['overnight']) {
            return $rates;
        }

        return array_filter($rates, function ($rate) use ($enabled) {
            return $enabled[\FS\Components\Shipping\RateProcessor\EnabledRateProcessor::$mapping[$rate['service']['flagship_code']]];
        });
    }
}

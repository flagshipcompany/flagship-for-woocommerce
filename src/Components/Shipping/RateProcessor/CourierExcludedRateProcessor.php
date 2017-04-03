<?php

namespace FS\Configurations\WordPress\RateProcessor;

use FS\Components\AbstractComponent;

class CourierExcludedRateProcessor extends AbstractComponent implements RateProcessorInterface
{
    public function getProcessedRates($rates, $payload = array())
    {
        $excluded = $payload['excluded'];

        return array_filter($rates, function ($rate) use ($excluded) {
            return !in_array(strtolower($rate['service']['courier_name']), $excluded);
        });
    }
}

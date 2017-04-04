<?php

namespace FS\Components\Shipping\RateProcessor\Factory;

use FS\Components\Shipping\RateProcessor;
use FS\Components\AbstractComponent;

class RateProcessorFactory extends AbstractComponent implements FactoryInterface
{
    public function getRateProcessor($resource, $context = array())
    {
        switch ($resource) {
            case 'NativeRate':
                return new RateProcessor\NativeRateProcessor();
                // no break
            case 'EnabledRate':
                return new RateProcessor\EnabledRateProcessor();
                // no break
            case 'CourierExcludedRate':
                return new RateProcessor\CourierExcludedRateProcessor();
                // no break
            case 'XNumberOfBestRate':
                return new RateProcessor\XNumberOfBestRateProcessor();
                // no break
            case 'ProcessRate':
                return new RateProcessor\ProcessRateProcessor();
                // no break
        }
    }
}

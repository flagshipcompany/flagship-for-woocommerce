<?php

namespace FS\Components\Shipping\RateProcessor\Factory;

use FS\Components\Shipping\RateProcessor;
use FS\Context\Factory\AbstractFactory;

class RateProcessorFactory extends AbstractFactory
{
    public function resolveWithoutContext($resource, array $option = [])
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

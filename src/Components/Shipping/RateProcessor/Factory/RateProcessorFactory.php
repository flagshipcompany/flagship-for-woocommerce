<?php

namespace FS\Components\Shipping\RateProcessor\Factory;

use FS\Components\AbstractComponent;

class RateProcessorFactory extends AbstractComponent implements FactoryInterface
{
    protected $driver;

    public function getRateProcessor($resource, $context = array())
    {
        switch ($resource) {
            case 'NativeRate':
                return new \FS\Components\Shipping\RateProcessor\NativeRateProcessor();
                // no break
            case 'EnabledRate':
                return new \FS\Components\Shipping\RateProcessor\EnabledRateProcessor();
                // no break
            case 'CourierExcludedRate':
                return new \FS\Components\Shipping\RateProcessor\CourierExcludedRateProcessor();
                // no break
            case 'XNumberOfBestRate':
                return new \FS\Components\Shipping\RateProcessor\XNumberOfBestRateProcessor();
                // no break
            case 'ProcessRate':
                return new \FS\Components\Shipping\RateProcessor\ProcessRateProcessor();
                // no break
        }
    }
}

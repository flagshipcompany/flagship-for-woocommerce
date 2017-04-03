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
                return new \FS\Configurations\WordPress\RateProcessor\NativeRateProcessor();
                // no break
            case 'EnabledRate':
                return new \FS\Configurations\WordPress\RateProcessor\EnabledRateProcessor();
                // no break
            case 'CourierExcludedRate':
                return new \FS\Configurations\WordPress\RateProcessor\CourierExcludedRateProcessor();
                // no break
            case 'XNumberOfBestRate':
                return new \FS\Configurations\WordPress\RateProcessor\XNumberOfBestRateProcessor();
                // no break
            case 'ProcessRate':
                return new \FS\Configurations\WordPress\RateProcessor\ProcessRateProcessor();
                // no break
        }
    }
}

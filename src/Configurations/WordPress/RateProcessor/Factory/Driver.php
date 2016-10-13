<?php

namespace FS\Configurations\WordPress\RateProcessor\Factory;

class Driver extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RateProcessor\Factory\FactoryInterface, \FS\Components\Factory\DriverInterface
{
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

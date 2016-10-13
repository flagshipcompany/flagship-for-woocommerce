<?php

namespace FS\Configurations\WordPress\RateProcessor;

class EnabledRateProcessor extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RateProcessor\RateProcessorInterface
{
	public function getProcessedRates($rates, $payload = array())
	{
        $enabled = $payload['enabled'];
        $mapping = array(
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

        if ($enabled['standard'] && $enabled['express'] && $enabled['overnight']) {
            return $rates;
        }

        return array_filter($rates, function ($rate) use ($enabled, $mapping) {
            return $enabled[$mapping[$rate['service']['flagship_code']]];
        });
	}
}

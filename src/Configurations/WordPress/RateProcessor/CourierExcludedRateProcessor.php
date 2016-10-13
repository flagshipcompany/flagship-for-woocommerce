<?php

namespace FS\Configurations\WordPress\RateProcessor;

class CourierExcludedRateProcessor extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RateProcessor\RateProcessorInterface
{
	public function getProcessedRates($rates, $payload = array())
	{
		$excluded = $payload['excluded'];

		return array_filter($rates, function($rate) use ($excluded) {
			return !in_array(strtoupper($rate['service']['courier_name']), $excluded);
		});
	}
}

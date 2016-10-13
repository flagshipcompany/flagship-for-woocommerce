<?php

namespace FS\Configurations\WordPress\RateProcessor;

class NativeRateProcessor extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RateProcessor\RateProcessorInterface
{
	public function getProcessedRates($rates, $payload = array())
	{
		$taxEnabled = $payload['taxEnabled'];
		$markup = $payload['payload'];
		$instanceId = $payload['instanceId'];
		$settings = $payload['settings'];

		$nativeRates = array();

		foreach ($rates as $rate) {
            $markupCost = ($markup['type'] == 'percentage') ? (floatval($rate['price']['subtotal']) * floatval($markup['rate']) / 100) : floatval($markup['rate']);
            $cost = $taxEnabled ? $rate['price']['total'] : $rate['price']['subtotal'];

            $nativeRate = array(
                'id' => $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'].'|'.$rate['service']['courier_name'].'|'.$rate['service']['courier_code'].'|'.$rate['service']['courier_desc'].'|'.strtotime($rate['service']['estimated_delivery_date']).'|'.$instanceId,
                'label' => $rate['service']['courier_name'].' - '.$rate['service']['courier_desc'],
                'cost' => number_format($cost + $markupCost, 2),
                'calc_tax' => 'per_order',
            );

            $nativeRates[] = $nativeRate;
        }

        return $nativeRates;
	}
}

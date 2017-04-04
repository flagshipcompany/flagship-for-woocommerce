<?php

namespace FS\Components\Shipping\RateProcessor;

use FS\Components\AbstractComponent;

class NativeRateProcessor extends AbstractComponent implements RateProcessorInterface
{
    public function getProcessedRates($rates, $payload = [])
    {
        $taxEnabled = $payload['taxEnabled'];
        $markup = $payload['markup'];
        $instanceId = $payload['instanceId'];
        $methodId = $payload['methodId'];

        $nativeRates = [];

        foreach ($rates as $rate) {
            $markupCost = ($markup['type'] == 'percentage') ? (floatval($rate['price']['subtotal']) * floatval($markup['rate']) / 100) : floatval($markup['rate']);
            $cost = $taxEnabled ? $rate['price']['total'] : $rate['price']['subtotal'];

            $nativeRate = [
                'id' => $methodId.'|'.$rate['service']['courier_name'].'|'.$rate['service']['courier_code'].'|'.$rate['service']['courier_desc'].'|'.strtotime($rate['service']['estimated_delivery_date']).'|'.$instanceId,
                'label' => $rate['service']['courier_name'].' - '.$rate['service']['courier_desc'],
                'cost' => number_format($cost + $markupCost, 2),
                'calc_tax' => 'per_order',
            ];

            $nativeRates[] = $nativeRate;
        }

        return $nativeRates;
    }
}

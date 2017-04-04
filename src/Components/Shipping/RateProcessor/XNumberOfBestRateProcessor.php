<?php

namespace FS\Components\Shipping\RateProcessor;

use FS\Components\AbstractComponent;

class XNumberOfBestRateProcessor extends AbstractComponent implements RateProcessorInterface
{
    public function getProcessedRates($rates, $payload = [])
    {
        $offered = $payload['offered'];
        $taxEnabled = $payload['taxEnabled'];

        uasort($rates, function ($rate1, $rate2) use ($taxEnabled) {
            $key = $taxEnabled ? 'total' : 'subtotal';

            if ($rate1['price'][$key] == $rate2['price'][$key]) {
                return 0;
            }

            return ($rate1['price'][$key] < $rate2['price'][$key]) ? -1 : 1;
        });

        if ($offered == 'all') {
            return $rates;
        }

        if ($offered == 'cheapest') {
            return [array_shift($rates)];
        }

        $length = min(intval($offered), count($rates));

        return array_slice($rates, 0, $length);
    }
}

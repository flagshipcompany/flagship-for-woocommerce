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
        $showTransitTime =  $payload['showTransitTime'];
        $fakeDiscountRate = $payload['fakeDiscountRate'];
        $extraMarkupForBox = $this->getExtraBoxMarkup($payload['extra_info']);

        $nativeRates = [];

        foreach ($rates as $rate) {
            $markupCost = ($markup['type'] == 'percentage') ? (floatval($rate['price']['subtotal']) * floatval($markup['rate']) / 100) : floatval($markup['rate']);

            if ($extraMarkupForBox) {
                $markupCost += $extraMarkupForBox;
            }

            $cost = $taxEnabled ? $rate['price']['total'] : $rate['price']['subtotal'];

            $transitTimeText = '';

            if ($showTransitTime === true) {
                $deliveryDate = $this->getDeliveryDate($rate['service']['estimated_delivery_date']);
                $transitTimeText = $this->makeTransitTimeText($deliveryDate);
            }

            $label = $rate['service']['courier_name'].' - '.$rate['service']['courier_desc'].$transitTimeText;

            if ($fakeDiscountRate > 0) {
                $label .= 'discount_rate='.$fakeDiscountRate;
            }

            $nativeRate = [
                'id' => $methodId.'|'.$rate['service']['courier_name'].'|'.$rate['service']['courier_code'].'|'.$rate['service']['courier_desc'].'|'.strtotime($rate['service']['estimated_delivery_date']).'|'.$instanceId,
                'label' => $label,
                'courier_name' => $rate['service']['courier_name'],
                'cost' => number_format($cost + $markupCost, 2, '.', ''),
                'calc_tax' => 'per_order',
            ];

            $nativeRates[] = $nativeRate;
        }

        return $nativeRates;
    }

    protected function getDeliveryDate($estimatedDeliveryTime)
    {
        $matched = preg_match('/[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/', $estimatedDeliveryTime, $deliveryDateMatches);

        return $matched ? $deliveryDateMatches[0] : '';
    }

    protected function makeTransitTimeText($deliveryDate)
    {
        if (!$deliveryDate) {
            return '';
        }

        $transitTime = ceil((strtotime($deliveryDate) - strtotime(date('Y-m-d')))/(24*60*60));
        $transitDaysText = $transitTime > 1 ? $transitTime . ' days' : $transitTime . ' day';
        $text = __(' (Time in transit '.$transitDaysText.')', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        return $text;
    }

    protected function getExtraBoxMarkup(array $extraInfo)
    {
        if (empty($extraInfo['boxes'])) {
            return;
        }

        $extraMarkup = 0;

        foreach ($extraInfo['boxes'] as $key => $box) {
            $extraMarkup += isset($box['markup']) ? $box['markup'] : 0;
        }

        return $extraMarkup;
    }
}

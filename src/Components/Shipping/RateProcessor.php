<?php

namespace FS\Components\Shipping;

class RateProcessor extends \FS\Components\AbstractComponent
{
    public function convertToWcShippingRate($rates, $instanceId = 0)
    {
        $rates = $this->filterByEnabledType($rates);

        $wcShippingRates = array();

        // prevent wrong arg being supplied
        if (!is_array($rates) || !$rates) {
            return $wcShippingRates;
        }

        $options = $this->ctx->getComponent('\\FS\\Components\\Options');
        $settings = $this->ctx->getComponent('\\FS\\Components\\Settings');

        $markup = array(
            'type' => $options->get('default_shipping_markup_type'),
            'rate' => $options->get('default_shipping_markup'),
        );

        $excluded = array_filter(array('fedex', 'ups', 'purolator'), function ($courier) use ($options) {
            return $options->not_equal('disable_courier_'.$courier, 'no');
        });

        foreach ($rates as $rate) {
            if (in_array(strtoupper($rate['service']['courier_name']), $excluded)) {
                continue;
            }

            $enableTax = ($options->get('apply_tax_by_flagship') == 'yes');

            $markupCost = ($markup['type'] == 'percentage') ? (floatval($rate['price']['subtotal']) * floatval($markup['rate']) / 100) : floatval($markup['rate']);
            $cost = $enableTax ? $rate['price']['total'] : $rate['price']['subtotal'];

            $wcShippingRate = array(
                'id' => $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'].'|'.$rate['service']['courier_name'].'|'.$rate['service']['courier_code'].'|'.$rate['service']['courier_desc'].'|'.strtotime($rate['service']['estimated_delivery_date']).'|'.$instanceId,
                'label' => $rate['service']['courier_name'].' - '.$rate['service']['courier_desc'],
                'cost' => number_format($cost + $markupCost, 2),
                'calc_tax' => 'per_order',
            );

            $wcShippingRates[] = $wcShippingRate;
        }

        uasort($wcShippingRates, function ($rate1, $rate2) {
            if ($rate1['cost'] == $rate2['cost']) {
                return 0;
            }

            return ($rate1['cost'] < $rate2['cost']) ? -1 : 1;
        });

        return $wcShippingRates;
    }

    protected function filterByEnabledType($rates)
    {
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

        $options = $this->ctx->getComponent('\\FS\\Components\\Options');

        $enabled = array(
            'standard' => ($options->get('allow_standard_rates') == 'yes'),
            'express' => ($options->get('allow_express_rates') == 'yes'),
            'overnight' => ($options->get('allow_overnight_rates') == 'yes'),
        );

        if ($enabled['standard'] && $enabled['express'] && $enabled['overnight']) {
            return $rates;
        }

        return array_filter($rates, function ($rate) use ($enabled, $mapping) {
            return $enabled[$mapping[$rate['service']['flagship_code']]];
        });
    }
}

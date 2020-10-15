<?php
namespace FlagshipWoocommerce;

use FlagshipWoocommerce\Requests\Rates_Request;
use FlagshipWoocommerce\Requests\ECommerce_Request;

class Cart_Rates_Processor {
    private $methodId;

    private $token;

    private $instanceSettings;

    private $rateOptions;

    private $rate_meta_data_extra_fields = array(
      'signature_required',
      'residential_receiver_address',
      'send_tracking_emails',
    );

    public function __construct($methodId, $token, $instanceSettings) {
        $this->methodId = $methodId;
        $this->token = $token;
        $this->instanceSettings = $instanceSettings;
        $this->rateOptions = $this->getRateOptions($this->instanceSettings);
    }

    public function fetchRates($package)
    {

        $debugMode = get_array_value($this->instanceSettings, 'debug_mode', 'no') == 'yes';
        $testEnv = get_array_value($this->instanceSettings,'test_env') == 'no' || get_array_value($this->instanceSettings,'test_env') == null ? 0 : 1;
        $ratesRequest = new Rates_Request($this->token, $debugMode, $testEnv);
        $rates = $ratesRequest->getRates($package, $this->instanceSettings);
        if(count($rates) != 0){
            $rates = $rates->all();
        }
        if (get_array_value($this->instanceSettings, 'offer_dhl_ecommerce_rates', null) == 'yes') {
            $eCommerceRequest = new ECommerce_Request($this->token, $debugMode);
            $eCommerceRates = $eCommerceRequest->getRates($package)->all();
            $rates = array_merge($eCommerceRates, $rates);
        }

        return $rates;
    }

    public function processRates($package, $rates)
    {
        if (count($rates) == 0) {
            return array();
        }

        $filteredRates = $this->filterRates($rates);
        $cartRates = array();

        foreach ($filteredRates as $key => $rate) {
            $cartRates[$key] = $this->makeCartRate($rate, $package);
        }

        usort($cartRates, array($this, 'sortRates'));

        return apply_filters('flagship_shipping_rates', $cartRates);
    }

    protected function getRateOptions($instanceSettings)
    {
        $optionValues = array_map(function($val) use ($instanceSettings) {
            if (get_array_value($instanceSettings, $val, 'no') == 'yes') {
                return true;
            }

            return false;
        }, $this->rate_meta_data_extra_fields);

        $options = array_combine($this->rate_meta_data_extra_fields, $optionValues);
        $options['box_split'] = get_array_value($instanceSettings, 'box_split', 'one_box');
        $options['box_split_weight'] = get_array_value($instanceSettings, 'box_split_weight', null);
        $options['token'] = get_array_value($instanceSettings, 'token', '');

        return  array_filter($options);
    }

    protected function filterRates($rates)
    {
        $filteredRates = array_filter($rates, array($this, 'filterRateByServiceType'));
        $filteredRates = array_filter($filteredRates, array($this, 'filterRateByCourier'));

        if ($this->isSettingChecked('only_show_cheapest', 'yes')) {
            $filteredRates = array($this->findCheapest($filteredRates));
        }

        return $filteredRates;
    }

    protected function makeCartRate($rate, $package)
    {
        $label =  $rate->getCourierName().' - '.$rate->getCourierDescription();

        if (get_array_value($this->instanceSettings, 'show_transit_time', 'no') == 'yes') {
            $label .= $this->makeTransitTimeText($rate->getDeliveryDate());
        }

        $metaData = $this->convertOptionsToMeta($this->rateOptions);
        $metaData['selected_shipping'] = $rate->getCourierName().' - '.$rate->getServiceCode();

        $cartRate = array(
            'id' => $this->methodId.'|'.$rate->getCourierName().'|'.$rate->getServiceCode(),
            'label' => $label,
            'cost' => $this->markupCost($rate->getSubtotal(), $package),
            'meta_data' => $metaData,
        );

        return $cartRate;
    }

    protected function markupCost($cost, $package)
    {
        if (get_array_value($this->instanceSettings, 'shipping_cost_markup_percentage', 0)) {
            $cost = $cost * (1 + $this->instanceSettings['shipping_cost_markup_percentage']/100);
        }

        if (get_array_value($this->instanceSettings, 'shipping_cost_markup_flat', 0)) {
            $cost = $cost + $this->instanceSettings['shipping_cost_markup_flat'];
        }

        $cost = $this->markupCostByShippingClass($cost, $package);

        return wc_format_decimal($cost);
    }

    protected function markupCostByShippingClass($cost, $package)
    {
        $shipping_classes = WC()->shipping()->get_shipping_classes();

        if (empty($shipping_classes)) {
            return $cost;
        }

        $found_shipping_classes = $this->findShippingClasses($package);

        foreach($found_shipping_classes as $shipping_class => $products) {
            $shipping_class_term = get_term_by('slug', $shipping_class, 'product_shipping_class');
            $class_cost_string = $shipping_class_term ? get_array_value($this->instanceSettings, 'class_cost_' . $shipping_class_term->term_id, '') : '';

            if ('' === $class_cost_string) {
                continue;
            }

            $cost += floatval($class_cost_string);
        }

        return $cost;
    }

    protected function findShippingClasses($package) {
        $found_shipping_classes = array();

        foreach ($package['contents'] as $item_id => $values) {
            if ($values['data']->needs_shipping()) {
                $found_class = $values['data']->get_shipping_class();

                if ( !isset( $found_shipping_classes[ $found_class ] ) ) {
                    $found_shipping_classes[ $found_class ] = array();
                }

                $found_shipping_classes[ $found_class ][ $item_id ] = $values;
            }
        }

        return $found_shipping_classes;
    }

    protected function filterRateByServiceType($rate)
    {
        $included = true;

        $settings = array(
            'offer_standard_rates',
            'offer_express_rates',
        );

        while ($included && $setting = array_shift($settings)) {
            preg_match('/offer_([a-zA-Z]+)_rates/', $setting, $matches);

            if ($matches[1] && $this->isSettingChecked($setting, 'no')) {
                $included = !($this->removeRateByCodeType($rate->getFlagshipCode(), $matches[1]));
            }
        }

        return $included;
    }

    protected function filterRateByCourier($rate)
    {
        $included = true;
        $couriers = FlagshipWoocommerceShipping::$couriers;

        while ($included && $courier = array_shift($couriers)) {
            $setting = 'disable_courier_'.$courier;

            if ($this->isSettingChecked($setting, 'yes')) {
                $included = $rate->getCourierName() != array_flip(FlagshipWoocommerceShipping::$couriers)[$courier];
            }
        }

        return $included;
    }

    protected function sortRates($a, $b)
    {
        if ($a['cost'] == $b['cost']) {
            return 0;
        }

        return ($a['cost'] < $b['cost']) ? -1 : 1;
    }

    protected function findCheapest($rates)
    {
        $cheapest = array_shift($rates);

        while ($nextRate = array_shift($rates)) {
            if ($nextRate->getTotal() < $cheapest->getTotal()) {
                $cheapest = $nextRate;
            }
        }

        return $cheapest;
    }

    protected function removeRateByCodeType($rateCode, $codeType)
    {
        $remove = false;

        switch ($codeType) {
            case 'standard':
                $removed = in_array($rateCode, array('standard', 'intlStandard'));
                break;
            case 'express':
                $removed = !in_array($rateCode, array('standard', 'intlStandard'));
                break;
        }

        return $removed;
    }

    protected function isSettingChecked($settingName, $checkedValue)
    {
        return isset($this->instanceSettings[$settingName]) && $this->instanceSettings[$settingName] == $checkedValue;
    }

    protected function convertOptionsToMeta($options)
    {
        $meta_data_fields = array_filter($options, function($key) {
          return in_array($key, $this->rate_meta_data_extra_fields);
        }, ARRAY_FILTER_USE_KEY);

        return array_map(function($val) {
            if (is_bool($val)) {
                return $val ? 'yes' : 'no';
            }

            return $val;
        }, $meta_data_fields);
    }

    protected function makeTransitTimeText($deliveryDate)
    {
        if (!$deliveryDate) {
            return '';
        }

        $transitTime = ceil((strtotime($deliveryDate) - strtotime(date('Y-m-d')))/(24*60*60));

        return sprintf(' - (%s: %s %s)', __('Time in transit', 'flagship-for-woocommerce'), $transitTime, _n("day", __("days", 'flagship-for-woocommerce'), $transitTime, 'flagship-for-woocommerce'));
    }
}

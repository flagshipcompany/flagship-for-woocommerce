<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Quoter extends Flagship_Component
{
    public function quote($package)
    {
        $rates = array();
        $has_receiver_address = $this->ctx['address']->has_receiver_address($package);

        if (!$has_receiver_address) {
            wc_add_notice('Add shipping address to get shipping rates! (click "Calculate Shipping")', 'notice');

            return $rates;
        }

        $request = $this->get_quote_request($package);

        $response = $this->ctx['client']->post(
            '/ship/rates',
            $request
        );

        if (!$response->is_success()) {
            wc_add_notice('Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>', 'error');
            wc_add_notice('<strong>Details:</strong><br/>'.$this->ctx['html']->ul($response->get_content()['errors']), 'error');
        }

        $rates = $this->get_processed_rates(
            $response->get_content()['content']
        );

        return $rates;
    }

    public function requote($order)
    {
        $request = $this->get_requote_request($order);
        $response = $this->ctx['client']->post(
            '/ship/rates',
            $request
        );

        if (!$response->is_success()) {
            $this->ctx['notification']->add('error', 'Unable to requote. Code '.$this->ctx['html']->ul($response->get_content()['errors']));

            return false;
        }

        $wc_shipping_rates = array();

        $rates = $this->get_processed_rates(
            $response->get_content()['content']
        );

        foreach ($rates as $rate) {
            $wc_shipping_rates[$rate['id']] = $rate['label'].' $'.$rate['cost'];
        }

        return $wc_shipping_rates;
    }

    public function get_processed_rates($rates)
    {
        $wc_shipping_rates = array();

        // prevent wrong arg being supplied
        if (!is_array($rates) || !$rates) {
            return $wc_shipping_rates;
        }

        $markup = array(
            'type' => $this->ctx['options']->get('default_shipping_markup_type'),
            'rate' => $this->ctx['options']->get('default_shipping_markup'),
        );

        $courier_exclusion = array();

        if ($this->ctx['options']->not_equal('disable_courier_fedex', 'no')) {
            $courier_exclusion[] = 'FEDEX';
        }

        if ($this->ctx['options']->not_equal('disable_courier_ups', 'no')) {
            $courier_exclusion[] = 'UPS';
        }

        if ($this->ctx['options']->not_equal('disable_courier_purolator', 'no')) {
            $courier_exclusion[] = 'PUROLATOR';
        }

        foreach ($rates as $rate) {
            if (in_array(strtoupper($rate['service']['courier_name']), $courier_exclusion)) {
                continue;
            }

            $wc_shipping_rates[] = array(
                'id' => FLAGSHIP_SHIPPING_PLUGIN_ID.'|'.$rate['service']['courier_name'].'|'.$rate['service']['courier_code'].'|'.$rate['service']['courier_desc'].'|'.strtotime($rate['service']['estimated_delivery_date']),
                'label' => $rate['service']['courier_name'].' - '.$rate['service']['courier_desc'],
                'cost' => $rate['price']['subtotal'] + ('percentage' ? $rate['price']['subtotal'] * $markup['rate'] / 100 : $markup['rate']),
                'calc_tax' => 'per_order', // we do not let WC compute tax
            );
        }

        uasort($wc_shipping_rates, array($this, 'rates_sort'));

        return $wc_shipping_rates;
    }

    public function rates_sort($rate_1, $rate_2)
    {
        if ($rate_1['cost'] == $rate_2['cost']) {
            return 0;
        }

        return ($rate_1['cost'] < $rate_2['cost']) ? -1 : 1;
    }

    protected function get_quote_request($package)
    {
        $request = array(
            'from' => $this->ctx['address']->get_from(),
            'to' => $this->ctx['address']->get_quote_to($package),
            'packages' => $this->ctx['package']->get_quote($package),
            'payment' => array(
                'payer' => 'F',
            ),
        );

        $isCountryNA = in_array($request['to']['country'], array('CA', 'US'));

        if ($isCountryNA) {
            $request['options']['address_correction'] = true;

            // a friendly fix for quote, when customer does not provide state
            // provide a possibly wrong state to let address correction correct it
            if (!$request['to']['state']) {
                $request['to']['state'] = $request['to']['country'] == 'CA' ? 'QC' : 'NY';
            }
        }

        return $request;
    }

    protected function get_requote_request($order)
    {
        $request = array(
            'from' => $this->ctx['address']->get_from(),
            'to' => $this->ctx['address']->get_order_to($order),
            'packages' => $this->ctx['package']->get_order($order),
            'payment' => array(
                'payer' => 'F',
            ),
        );

        return $request;
    }
}

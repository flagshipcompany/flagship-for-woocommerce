<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Confirmation extends Flagship_Component
{
    public function confirm($order)
    {
        $this->ctx['notification']->scope('shop_order', array('id' => $order->id));

        $shipment = $this->ctx['order']->get_meta('flagship_shipping_raw');

        if ($shipment) {
            $this->ctx['notification']->add('warning', 'You have flagship shipment for this order. SmartshipID ('.$shipment['shipment_id'].')');

            return false;
        }

        $overload_shipping_method = isset($_POST['flagship_shipping_service']) ? sanitize_text_field($_POST['flagship_shipping_service']) : null;

        $request = $this->get_confirmation_request($order, $overload_shipping_method);

        $response = $this->ctx['client']->post(
            '/ship/confirm',
            $request
        );

        $shipping = $response->get_content();

        if ($shipping['errors']) {
            $this->ctx['notification']->add('error', $this->ctx['html']->ul($shipping['errors']));

            return false;
        }

        return $shipping;
    }

    protected function get_confirmation_request($order, $method = null)
    {
        if ($method) {
            list($provider, $courier_name, $courier_code, $courier_desc, $date) = explode('|', $method);
            $service = array(
                'provider' => $provider,
                'courier_name' => strtolower($courier_name),
                'courier_code' => $courier_code,
                'courier_desc' => $courier_desc,
                'date' => $date,
            );
        } else {
            $service = $this->ctx['order']->get_shipping_service($order);
        }

        unset($service['provider']);
        unset($service['date']);
        unset($service['courier_desc']);

        $request = array(
            'from' => $this->ctx['address']->get_from(),
            'to' => $this->ctx['address']->get_order_to($order),
            'packages' => $this->ctx['package']->get_order($order),
            'payment' => array(
                'payer' => 'F',
            ),
            'service' => $service,
        );

        if (empty($request['to']['name'])) {
            $request['to']['name'] = $request['to']['attn'] ?: 'Receiver';
        }

        if ($options = $this->get_options()) {
            $request['options'] = $options;
        }

        if ($request['to']['country'] != 'CA') {
            $request['sold_to'] = $this->get_sold_to($request);
            $request['inquiry'] = $this->get_inquiry($request);
            $request['declared_items'] = $this->get_declared_items($order);
        }

        return $request;
    }

    protected function get_options()
    {
        $options = array();

        if (isset($_REQUEST['flagship_shipping_enable_insurance'])
            && $_REQUEST['flagship_shipping_enable_insurance'] == 'yes'
            && $_REQUEST['flagship_shipping_insurance_value'] > 0
            && $_REQUEST['flagship_shipping_insurance_description']
        ) {
            $options['insurance'] = array(
                'value' => sanitize_text_field($_REQUEST['flagship_shipping_insurance_value']),
                'description' => sanitize_text_field($_REQUEST['flagship_shipping_insurance_description']),
            );
        }

        if (isset($_REQUEST['flagship_shipping_enable_cod'])
            && $_REQUEST['flagship_shipping_enable_cod'] == 'yes'
            && $_REQUEST['flagship_shipping_cod_method']
            && $_REQUEST['flagship_shipping_cod_payable_to']
            && $_REQUEST['flagship_shipping_cod_receiver_phone']
            && $_REQUEST['flagship_shipping_cod_amount']
            && $_REQUEST['flagship_shipping_cod_currency']
        ) {
            $options['cod'] = array(
                'method' => sanitize_text_field($_REQUEST['flagship_shipping_cod_method']),
                'payable_to' => sanitize_text_field($_REQUEST['flagship_shipping_cod_payable_to']),
                'receiver_phone' => sanitize_text_field($_REQUEST['flagship_shipping_cod_receiver_phone']),
                'amount' => sanitize_text_field($_REQUEST['flagship_shipping_cod_amount']),
                'currency' => sanitize_text_field($_REQUEST['flagship_shipping_cod_currency']),
            );
        }

        if (isset($_REQUEST['flagship_shipping_signature_required'])) {
            $options['signature_required'] = $_REQUEST['flagship_shipping_signature_required'] == 'yes';
        }

        if (isset($_REQUEST['flagship_shipping_reference'])
            && $_REQUEST['flagship_shipping_reference']) {
            $options['reference'] = sanitize_text_field($_REQUEST['flagship_shipping_reference']);
        }

        if (isset($_REQUEST['flagship_shipping_driver_instructions'])
            && $_REQUEST['flagship_shipping_driver_instructions']) {
            $options['driver_instructions'] = sanitize_text_field($_REQUEST['flagship_shipping_driver_instructions']);
        }

        if (isset($_REQUEST['flagship_shipping_date'])
            && strtotime($_REQUEST['flagship_shipping_date']) >= strtotime(date('Y-m-d'))
        ) {
            $options['shipping_date'] = sanitize_text_field($_REQUEST['flagship_shipping_date']);
        }

        return $options;
    }

    protected function get_sold_to($request)
    {
        $sold_to = array(
            'sold_to_address' => $request['to'],
            'duties_payer' => 'C', // receiver pay duties
            'reason_for_export' => 'P',
        );

        return $sold_to;
    }

    protected function get_inquiry($request)
    {
        $inquiry = array(
            'company' => $request['from']['name'],
            'name' => $request['from']['attn'],
            'inquiry_phone' => preg_replace('(\D)', '', $request['from']['phone']),
        );

        return $inquiry;
    }

    protected function get_declared_items($order)
    {
        $items = array();
        $items['currency'] = strtoupper(get_woocommerce_currency());

        $order_items = $order->get_items();

        foreach ($order_items as $order_item) {
            $product = $order->get_product_from_item($order_item);

            $items['ci_items'][] = array(
                'product_name' => $product->get_title(),
                'description' => substr(get_post($product->id)->post_content, 0, 50),
                'country_of_origin' => 'CA',
                'quantity' => $order_item['qty'],
                'unit_price' => $product->price,
                'unit_weight' => max(1, ceil(woocommerce_get_weight($product->get_weight(), 'kg'))),
                'unit_of_measurement' => 'kilogram',
            );
        }

        return $items;
    }
}

<?php

namespace FS\Components\Shipping\RequestBuilder\Drivers\WordPress\Order;

class ShippingOptionsBuilder extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    public function build($payload = null)
    {
        $shippingOptions = array();

        if (in_array($payload['to']['country'], array('CA', 'US'))) {
            $shippingOptions['address_correction'] = true;
        }

        $request = $payload['request']->request;

        if ($request->has('flagship_shipping_enable_insurance')
            && $request->get('flagship_shipping_enable_insurance') == 'yes'
            && $request->get('flagship_shipping_insurance_value') > 0
            && $request->get('flagship_shipping_insurance_description')
        ) {
            $shippingOptions['insurance'] = array(
                'value' => $request->get('flagship_shipping_insurance_value'),
                'description' => $request->get('flagship_shipping_insurance_description'),
            );
        }

        if ($request->has('flagship_shipping_enable_cod')
            && $request->get('flagship_shipping_enable_cod') == 'yes'
            && $request->get('flagship_shipping_cod_method')
            && $request->get('flagship_shipping_cod_payable_to')
            && $request->get('flagship_shipping_cod_receiver_phone')
            && $request->get('flagship_shipping_cod_amount')
            && $request->get('flagship_shipping_cod_currency')
        ) {
            $shippingOptions['cod'] = array(
                'method' => $request->get('flagship_shipping_cod_method'),
                'payable_to' => $request->get('flagship_shipping_cod_payable_to'),
                'receiver_phone' => $request->get('flagship_shipping_cod_receiver_phone'),
                'amount' => $request->get('flagship_shipping_cod_amount'),
                'currency' => $request->get('flagship_shipping_cod_currency'),
            );
        }

        if ($request->has('flagship_shipping_signature_required')) {
            $shippingOptions['signature_required'] = $request->get('flagship_shipping_signature_required') == 'yes';
        }

        if ($request->has('flagship_shipping_reference')
            && $request->get('flagship_shipping_reference')) {
            $shippingOptions['reference'] = $request->get('flagship_shipping_reference');
        }

        if ($request->has('flagship_shipping_driver_instructions')
            && $request->get('flagship_shipping_driver_instructions')) {
            $shippingOptions['driver_instructions'] = $request->get('flagship_shipping_driver_instructions');
        }

        if ($request->has('flagship_shipping_date')
            && strtotime($request->get('flagship_shipping_date')) >= strtotime(date('Y-m-d'))
        ) {
            $shippingOptions['shipping_date'] = $request->get('flagship_shipping_date');
        }

        if ($payload['to']['country'] != 'CA') {
            $shippingOptions['sold_to'] = array(
                'sold_to_address' => $payload['to'],
                'duties_payer' => 'C', // receiver pay duties
                'reason_for_export' => 'P',
            );

            $shippingOptions['inquiry'] = array(
                'company' => $payload['from']['name'],
                'name' => $payload['from']['attn'],
                'inquiry_phone' => preg_replace('(\D)', '', $payload['from']['phone']),
            );

            $shippingOptions['declared_items'] = $this->getDeclaredItems($payload['order']);
        }

        return $shippingOptions;
    }

    protected function getDeclaredItems($order)
    {
        $items = array();
        $items['currency'] = strtoupper(get_woocommerce_currency());

        $order_items = $order->getWcOrder()->get_items();

        foreach ($order_items as $order_item) {
            $product = $order->getWcOrder()->get_product_from_item($order_item);

            $description = substr(get_post($product->id)->post_content, 0, 50);

            $items['ci_items'][] = array(
                'product_name' => $product->get_title(),
                'description' => (!empty($description) ? $description : ''),
                'country_of_origin' => 'CA',
                'quantity' => $order_item['qty'],
                'unit_price' => $product->get_price(),
                'unit_weight' => max(1, ceil(woocommerce_get_weight($product->get_weight(), 'kg'))),
                'unit_of_measurement' => 'kilogram',
            );
        }

        return $items;
    }
}

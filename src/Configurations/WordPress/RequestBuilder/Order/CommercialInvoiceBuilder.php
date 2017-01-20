<?php

namespace FS\Configurations\WordPress\RequestBuilder\Order;

class CommercialInvoiceBuilder extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    public function build($payload = null)
    {
        $ci = array();
        $ci['sold_to'] = array(
            'sold_to_address' => $payload['to'],
            'duties_payer' => 'C', // receiver pay duties
            'reason_for_export' => 'P',
        );

        $ci['inquiry'] = array(
            'company' => $payload['from']['name'],
            'name' => $payload['from']['attn'],
            'inquiry_phone' => preg_replace('(\D)', '', $payload['from']['phone']),
        );

        $ci['declared_items'] = $this->getDeclaredItems($payload['order']);

        return $ci;
    }

    protected function getDeclaredItems($order)
    {
        $items = array();
        $items['currency'] = strtoupper(get_woocommerce_currency());

        $order_items = $order->getNativeOrder()->get_items();

        foreach ($order_items as $order_item) {
            $product = $order->getNativeOrder()->get_product_from_item($order_item);

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

<?php

namespace FS\Components\Shipping\Request\Builder\Order;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\Request\Builder\BuilderInterface;

class CommercialInvoiceBuilder extends AbstractComponent implements BuilderInterface
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

        $ci['declared_items'] = $this->getDeclaredItems($payload['shipping']->getOrder());

        return $ci;
    }

    protected function getDeclaredItems($order)
    {
        $items = array();
        $items['currency'] = strtoupper(get_woocommerce_currency());

        $order_items = $order->native()->get_items();

        foreach ($order_items as $order_item) {
            $product = $order->native()->get_product_from_item($order_item);

            $description = substr(get_post($product->get_id())->post_content, 0, 50);

            $items['ci_items'][] = array(
                'product_name' => substr($product->get_title(),0,29),
                'description' => substr((!empty($description) ? $description : ''),0,29),
                'country_of_origin' => 'CA',
                'HS_code' => $product->get_attribute('hs-code'),
                'quantity' => $order_item['qty'],
                'unit_price' => $product->get_price(),
                'unit_weight' => round(wc_get_weight($product->get_weight(), 'kg'), 2),
                'unit_of_measurement' => 'kilogram',
            );
        }

        return $items;
    }
}

<?php
namespace FlagshipWoocommerce\Requests;

use Flagship\Shipping\Flagship;
use FlagshipWoocommerce\Helpers\Product_Helper;

class Commercial_Inv_Request_Helper {

    public function makeIntShpFields($prepareRequest, $order)
    {
        $currency = get_option('woocommerce_currency');
        $ciItems = $this->makeCommercialInvItems($order);

        if (empty($ciItems)) {
            return array();
        }

        $declaredItems = array(
            'currency' => $currency,
            'ci_items' => $ciItems,
        );
        $soldTo = array(
            'sold_to_address' => $prepareRequest['to'],
            'duties_payer' => 'F',
            'reason_for_export' => 'P',
        );
        $inquiry = array(
            'company' => $prepareRequest['from']['name'],
            'name' => $prepareRequest['from']['attn'],
            'inquiry_phone' => $prepareRequest['from']['phone'],
        );

        return array(
            'sold_to' => $soldTo,
            'inquiry' => $inquiry,
            'declared_items' => $declaredItems,
        );
    }

    protected function makeCommercialInvItems($order)
    {
        $unit = get_option('woocommerce_weight_unit');
        $orderItems = $order->get_items();
        $commercialInvItems = array();

        foreach ( $orderItems as $items_key => $item_data ) {
            $product = $item_data->get_product();
            $ciItem = array();
            $ciItem['product_name'] = $product->get_name();
            $description = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
            $ciItem['description'] = substr($description, 0, 50);
            $ciItem['unit_price'] = $product->get_price();
            $ciItem['unit_weight'] = $product->get_weight() ? round(wc_get_weight($product->get_weight(), 'lbs', $unit)) : 1;
            $ciItem['unit_of_measurement'] = 'imperial';
            $ciItem['quantity'] = $item_data->get_quantity();
            $ciItem['country_of_origin'] = get_post_meta($product->get_id(), Product_Helper::$fields['country'], true);
            $ciItem['HS_code'] = trim(get_post_meta($product->get_id(), Product_Helper::$fields['hs'], true));
            $commercialInvItems[] = $ciItem;
        }

        $itemsWithCountry = array_filter($commercialInvItems, function($val) {
            return !empty($val['country_of_origin']) && !empty($val['description']);
        });

        if (count($itemsWithCountry) != count($commercialInvItems)) {
            return array();
        }

        return $commercialInvItems;
    }
}
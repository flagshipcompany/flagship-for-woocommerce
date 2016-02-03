<?php

class Flagship_Request_Formatter
{
    public static function get_quoting_product_items($package)
    {
        $product_items = array();

        foreach ($package['contents'] as $id => $item) {
            if (!$item['data']->needs_shipping()) {
                continue;
            }

            if (!$item['data']->get_weight()) {
                wc_add_notice('Product '.$item['data']->get_title().' is missing weight, weight default to 1 lbs.', 'notice');
            }

            $count = 0;

            list(
                $width,
                $length,
                $height,
                $weight
            ) = self::get_product_dimensions($item['data']);

            do {
                $product_items[] = array(
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    'weight' => $weight,
                );

                ++$count;
            } while ($count < $item['quantity']);
        }

        return $product_items;
    }

    public static function get_confirmation_product_items($order)
    {
        $order_items = $order->get_items();
        $product_items = array();

        foreach ($order_items as $order_item) {
            $product = $order->get_product_from_item($order_item);

            $count = 0;

            list(
                $width,
                $length,
                $height,
                $weight
            ) = self::get_product_dimensions($product);

            do {
                $product_items[] = array(
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    'weight' => $weight,
                );

                ++$count;
            } while ($count < $order_item['qty']);
        }

        return $product_items;
    }

    public static function get_product_dimensions($product)
    {
        return array(
            $width = $product->width ? max(1, ceil(woocommerce_get_dimension($product->width, 'in'))) : 1,
            $length = $product->length ? max(1, ceil(woocommerce_get_dimension($product->length, 'in'))) : 1,
            $height = $product->height ? max(1, ceil(woocommerce_get_dimension($product->height, 'in'))) : 1,
            $weight = $product->has_weight() ? max(1, ceil(woocommerce_get_weight($product->get_weight(), 'lbs'))) : 1,
        );
    }

    public static function get_flagship_shipping_service($order)
    {
        $shipping_methods = $order->get_shipping_methods();

        list($provider, $courier_name, $courier_code) = explode(':', $shipping_methods[key($shipping_methods)]['method_id']);

        return array(
            'provider' => $provider,
            'courier_name' => strtolower($courier_name),
            'courier_code' => $courier_code,
        );
    }

    public static function get_quote_request($package)
    {
        $flagship = Flagship_Application::get_instance();

        $request = array(
            'from' => self::get_address_from(),
            'to' => self::get_address_to($package),
            'packages' => array(
                'items' => self::get_package_items(self::get_quoting_product_items($package)),
                'units' => 'imperial',
                'type' => 'package',
            ),
            'payment' => array(
                'payer' => 'F',
            ),
        );

        return $request;
    }

    public static function get_confirmation_request($order)
    {
        $service = self::get_flagship_shipping_service($order);

        unset($service['provider']);

        $request = array(
            'from' => self::get_address_from(),
            'to' => array(
                'name' => $order->shipping_company,
                'attn' => $order->shipping_first_name.' '.$order->shipping_last_name,
                'address' => trim($order->shipping_address_1.' '.$order->shipping_address_2),
                'city' => $order->shipping_city,
                'state' => $order->shipping_state,
                'country' => $order->shipping_country,
                'postal_code' => $order->shipping_postcode,
                'phone' => $order->billing_phone, // no such a field in the shipping!?
            ),
            'packages' => array(
                'items' => self::get_package_items(self::get_confirmation_product_items($order)),
                'units' => 'imperial',
                'type' => 'package',
            ),
            'payment' => array(
                'payer' => 'F',
            ),
            'service' => $service,
        );

        return $request;
    }

    public static function get_address_from()
    {
        $flagship = Flagship_Application::get_instance();

        $address = array(
            'country' => 'CA',
            'state' => $flagship->get_option('freight_shipper_state'),
            'city' => $flagship->get_option('freight_shipper_city'),
            'postal_code' => $flagship->get_option('origin'),
            'address' => $flagship->get_option('freight_shipper_street'),
            'name' => $flagship->get_option('shipper_company_name'),
            'attn' => $flagship->get_option('shipper_person_name'),
            'phone' => $flagship->get_option('shipper_phone_number'),
            'ext' => $flagship->get_option('shipper_phone_ext'),
        );

        return $address;
    }

    public static function get_address_to($package)
    {
        $address = array(
            'country' => $package['destination']['country'],
            'state' => $package['destination']['state'],
            'city' => $package['destination']['city'],
            'postal_code' => $package['destination']['postcode'],
            'address' => $package['destination']['address'].' '.$package['destination']['address_2'],
        );

        return $address;
    }

    public static function get_package_items(array $product_items)
    {
        $flagship = Flagship_Application::get_instance();

        $package_box_max_weight = (int) $flagship->get_option('default_package_box_split_weight', 20);
        $package_item_in_same_box = $flagship->get_option('default_package_box_split', 'no') == 'yes';

        $items = array();

        // add first product item(box) into package items
        $product_item = array_shift($product_items);
        $items[] = array(
            'width' => 1,
            'height' => 1,
            'length' => 1,
            'weight' => $product_item['weight'],
            'description' => 'Flagship shipping package',
        );

        // if all product items must be packed into one box
        // sum up total weight
        if ($package_item_in_same_box) {
            foreach ($product_items as $product_item) {
                $items[0]['weight'] += $product_item['weight'];
            }

            return $items;
        }

        // product items need to be packed into boxes
        while ($product_items) {
            $product_item = array_shift($product_items);
            $fit_into_existing = false;

            // iterate through all existing box to check whether we can fit the current product item into one of the box
            foreach ($items as &$item) {
                if ($package_box_max_weight >= $item['weight'] + $product_item['weight']) {
                    $item['weight'] += $product_item['weight'];
                    $fit_into_existing = true;

                    break;
                }
            }

            // make new box if we cannot fit current product item into any of the existing box
            if (!$fit_into_existing) {
                $items[] = array(
                    'width' => 1,
                    'height' => 1,
                    'length' => 1,
                    'weight' => $product_item['weight'],
                    'description' => 'Flagship shipping package',
                );
            }
        }

        return $items;
    }

    public static function get_processed_rates($rates, $id)
    {
        $wc_shipping_rates = array();

        $flagship = Flagship_Application::get_instance();

        $markup = array(
            'type' => $flagship->get_option('default_shipping_markup_type'),
            'rate' => $flagship->get_option('default_shipping_markup'),
        );

        foreach ($rates as $rate) {
            $wc_shipping_rates[] = array(
                'id' => $id.':'.$rate['service']['courier_name'].':'.$rate['service']['courier_code'],
                'label' => $rate['service']['courier_name'].' '.$rate['service']['courier_desc'].' <small>'.date('M. d', strtotime($rate['service']['estimated_delivery_date'])).'</small>',
                'cost' => $rate['price']['total'] + ('percentage' ? $rate['price']['total'] * $markup['rate'] / 100 : $markup['rate']),
                'taxes' => false, // we do not let WC compute tax
            );
        }

        uasort($wc_shipping_rates, array(__CLASS__, 'rates_sort'));

        return $wc_shipping_rates;
    }

    public static function rates_sort($rate_1, $rate_2)
    {
        if ($rate_1['cost'] == $rate_2['cost']) {
            return 0;
        }

        return ($rate_1['cost'] < $rate_2['cost']) ? -1 : 1;
    }
}

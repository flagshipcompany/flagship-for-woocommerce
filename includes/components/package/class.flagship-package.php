<?php

class Flagship_Package
{
    protected $flagship;

    public function __construct(Flagship_Application $flagship)
    {
        $this->flagship = $flagship;
    }

    public function get_quote($package)
    {
        $packages = array(
            'items' => $this->get_package_items($this->get_quote_product_items($package)),
            'units' => 'imperial',
            'type' => 'package',
        );

        return $packages;
    }

    public function get_order($order)
    {
        $packages = array(
            'items' => $this->get_package_items($this->get_confirmation_product_items($order)),
            'units' => 'imperial',
            'type' => 'package',
        );

        return $packages;
    }

    protected function get_package_items(array $product_items)
    {
        $package_box_max_weight = (int) $this->flagship['options']->get('default_package_box_split_weight', 20);
        $package_item_in_same_box = $this->flagship['options']->get('default_package_box_split', 'no') == 'yes';

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

    protected function get_quote_product_items($package)
    {
        $product_items = array();

        $notices = array();

        foreach ($package['contents'] as $id => $item) {
            if (!$item['data']->needs_shipping()) {
                continue;
            }

            if (!$item['data']->get_weight()) {
                $notices[] = 'Product '.$item['data']->get_title().' is missing weight, weight default to 1 lbs.';
            }

            $count = 0;

            list(
                $width,
                $length,
                $height,
                $weight
            ) = $this->get_product_dimensions($item['data']);

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

        wc_add_notice(implode('<br/>', $notices), 'notice');

        return $product_items;
    }

    protected function get_confirmation_product_items($order)
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
            ) = $this->get_product_dimensions($product);

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

    protected function get_product_dimensions($product)
    {
        return array(
            $width = $product->width ? max(1, ceil(woocommerce_get_dimension($product->width, 'in'))) : 1,
            $length = $product->length ? max(1, ceil(woocommerce_get_dimension($product->length, 'in'))) : 1,
            $height = $product->height ? max(1, ceil(woocommerce_get_dimension($product->height, 'in'))) : 1,
            $weight = $product->has_weight() ? max(1, ceil(woocommerce_get_weight($product->get_weight(), 'lbs'))) : 1,
        );
    }
}

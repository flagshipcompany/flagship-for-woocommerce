<?php

namespace FS\Components\Shipping\RequestBuilder;

abstract class AbstractPackageItemsBuilder extends \FS\Components\AbstractComponent implements RequestBuilderInterface
{
    public static $scope = 'prototype';

    public function build($payload = null)
    {
        $packages = array(
            'items' => $this->makePackageItems($this->makeProductItems($payload), $payload),
            'units' => 'imperial',
            'type' => 'package',
        );

        return $packages;
    }

    protected function makePackageItems($product_items, $payload)
    {
        $options = $payload['options'];

        $package_box_max_weight = (int) $options->get('default_package_box_split_weight', 20);
        $package_item_in_same_box = $options->get('default_package_box_split', 'no') == 'yes';

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

    protected function getProductDimensions($product)
    {
        if (!$product) {
            return array(1, 1, 1, 1);
        }

        return array(
            $product->width ? max(1, ceil(woocommerce_get_dimension($product->width, 'in'))) : 1,
            $product->length ? max(1, ceil(woocommerce_get_dimension($product->length, 'in'))) : 1,
            $product->height ? max(1, ceil(woocommerce_get_dimension($product->height, 'in'))) : 1,
            $product->weight ? max(1, ceil(woocommerce_get_weight($product->weight, 'lbs'))) : 1,
        );
    }
}

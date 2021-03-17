<?php

namespace FS\Components\Shipping\Request\Builder;

use FS\Components\AbstractComponent;

abstract class AbstractPackageItemsBuilder extends AbstractComponent implements BuilderInterface
{
    public static $scope = 'prototype';

    protected $boxes = [];

    public function build($payload = null)
    {
        $packages = [
            'items' => $this->makePackageItems($this->makeProductItems($payload), $payload),
            'units' => 'imperial',
            'type' => 'package',
        ];

        // verify if each package item's weight is at least 1 lb
        foreach ($packages['items'] as $key => $item) {
            if ($item['weight'] < 1) {
                $packages['items'][$key]['weight'] = 1;
            }
        }

        if ($this->boxes) {
            $packages['extra_info'] = ['boxes' => $this->boxes];
        }

        return $packages;
    }

    protected function makePackageItems($product_items, $payload)
    {
        $options = $payload['options'];

        $package_box_max_weight = (int) $options->get('default_package_box_split_weight', 20);
        $package_item_in_same_box = $options->get('default_package_box_split', 'no') == 'yes';
        $package_one_box_per_item = $options->get('default_package_box_split', 'no') == 'each';

        $items = [];

        //If one box per item
        if ($package_one_box_per_item) {
            foreach ($product_items as $key => $product_item) {
                $items[$key] = [
                    'width' => $product_item['width'],
                    'height' => $product_item['height'],
                    'length' => $product_item['length'],
                    'weight' => $product_item['weight'],
                    'description' => isset($product_item['sku_no']) ? $product_item['sku_no'] : 'Flagship shipping package',
                ];
            }

            return $items;
        }

        // add first product item(box) into package items
        $product_item = array_shift($product_items);
        $items[] = [
            'width' => $product_item['width'],
            'height' => $product_item['height'],
            'length' => $product_item['length'],
            'weight' => $product_item['weight'],
            'description' => isset($product_item['sku_no']) ? $product_item['sku_no'] : 'Flagship shipping package',
        ];

        // if all product items must be packed into one box
        // sum up total weight and take the largest dimension of all items so the rate estimates can be as accurate as possible
        if ($package_item_in_same_box) {
            foreach ($product_items as $product_item) {
                $items[0]['weight'] += $product_item['weight'];
                $items[0]['width'] = (int) $product_item['width'] > (int) $items[0]['width'] ? $product_item['width'] : $items[0]['width'];
                $items[0]['height'] = (int) $product_item['height'] > (int) $items[0]['height'] ? $product_item['height'] : $items[0]['height'];
                $items[0]['length'] = (int) $product_item['length'] > (int) $items[0]['length'] ? $product_item['length'] : $items[0]['length'];
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
                $items[] = [
                    'width' => $product_item['width'],
                    'height' => $product_item['height'],
                    'length' => $product_item['length'],
                    'weight' => $product_item['weight'],
                    'description' => isset($product_item['sku_no']) ? $product_item['sku_no'] : 'Flagship shipping package',
                ];
            }
        }

        return $items;
    }

    protected function getProductDimensions($product)
    {
        if (!$product) {
            return [1, 1, 1, 1];
        }

        return array(
            $product->get_width() ? max(1, ceil(wc_get_dimension($product->get_width(), 'in'))) : 1,
            $product->get_length() ? max(1, ceil(wc_get_dimension($product->get_length(), 'in'))) : 1,
            $product->get_height() ? max(1, ceil(wc_get_dimension($product->get_height(), 'in'))) : 1,
            // when product weight is not defined, default to 0.001 lb (in accordance with shopify client "1 gram")
            $product->has_weight() ? (float) wc_get_weight($product->get_weight(), 'lbs') : 0.001,
            $product->get_sku() ? $product->get_sku() : $product->get_id(),
        );
    }
}

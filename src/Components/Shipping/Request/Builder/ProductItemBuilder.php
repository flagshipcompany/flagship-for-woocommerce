<?php

namespace FS\Components\Shipping\Request\Builder;

use FS\Components\AbstractComponent;

class ProductItemBuilder extends AbstractComponent implements BuilderInterface
{
    public function build($payload = null)
    {
        $dimension_unit = get_option('woocommerce_dimension_unit');
        $weight_unit = get_option('woocommerce_weight_unit');
        $output_weight_unit = isset($options['weight_unit']) ? $options['weight_unit'] : 'lbs';
        $output_dimension_unit = isset($options['dimension_unit']) ? $options['dimension_unit'] : 'in';
        $items = [];
        foreach ($payload['productItems'] as $productItem) {
            $items[] = [
                'length' => wc_get_dimension($productItem['length'], $output_dimension_unit, $dimension_unit),
                'width' => wc_get_dimension($productItem['width'], $output_dimension_unit, $dimension_unit),
                'height' => wc_get_dimension($productItem['height'], $output_dimension_unit, $dimension_unit),
                'weight' => wc_get_weight($productItem['weight'], $output_weight_unit, $weight_unit),
                'description' => isset($productItem['sku_no']) ? $productItem['sku_no'] : '',
            ];
        }

        return $items;
    }
}

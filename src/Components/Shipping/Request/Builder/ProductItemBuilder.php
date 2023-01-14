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
                'length' => $productItem['length'], 
                'width' => $productItem['width'], 
                'height' => $productItem['height'],
                'weight' => $productItem['weight'],
                'description' => isset($productItem['sku_no']) ? $productItem['sku_no'] : '',
            ];
        }

        return $items;
    }
}

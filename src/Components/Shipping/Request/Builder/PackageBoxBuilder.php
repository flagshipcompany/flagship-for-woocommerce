<?php

namespace FS\Components\Shipping\Request\Builder;

use FS\Components\AbstractComponent;

class PackageBoxBuilder extends AbstractComponent implements BuilderInterface
{
    public static function format_package_boxes($boxes)
    {
        $boxes = array_map(function($box) {
            $box['inner_length'] = self::make_inner_dimension($box, 'length');
            $box['inner_width'] = self::make_inner_dimension($box, 'width');
            $box['inner_height'] = self::make_inner_dimension($box, 'height');
            $box['weight'] = isset($box['weight']) ? $box['weight'] : 0;

            return $box;
        }, $boxes);

        return $boxes;
    }

    public static function make_inner_dimension($box, $outer_dimension)
    {
        $inner_dimension = 'inner_'.$outer_dimension;

        if (isset($box[$inner_dimension])) {
            return $box[$inner_dimension];
        }

        return null;
    }

    public static function addOptionalValues($packageBox, $boxValue, $forSaving = false)
    {
        $dimension_unit = get_option('woocommerce_dimension_unit');
        $weight_unit = get_option('woocommerce_weight_unit');
        $output_weight_unit = isset($options['weight_unit']) ? $options['weight_unit'] : 'lbs';
        $output_dimension_unit = isset($options['dimension_unit']) ? $options['dimension_unit'] : 'in';
        
        foreach (['inner_length', 'inner_width', 'inner_height'] as $key => $value) {
            if (isset($boxValue[$value]) && $boxValue[$value] > 0) {
                $packageBox[$value] = round(wc_get_dimension($boxValue[$value], $output_dimension_unit, $dimension_unit),0,PHP_ROUND_HALF_EVEN);
            }
        }

        if (!$forSaving) {
            return $packageBox;
        }

        if (isset($boxValue['shipping_classes'])) {
            $packageBox['shipping_classes'] = $boxValue['shipping_classes'];
        }

        if (isset($boxValue['weight'])) {
            $packageBox['weight'] = wc_get_weight($boxValue['weight'], $output_weight_unit, $weight_unit);
        }

        return $packageBox;
    }

    public function build($payload = null)
    {
        $boxes = $payload['options']->get('package_box');
        $dimension_unit = get_option('woocommerce_dimension_unit');
        $weight_unit = get_option('woocommerce_weight_unit');
        $output_weight_unit = isset($options['weight_unit']) ? $options['weight_unit'] : 'lbs';
        $output_dimension_unit = isset($options['dimension_unit']) ? $options['dimension_unit'] : 'in';
        
        $packageBoxes = [];

        foreach ($boxes as $box) {
            $packageBox = [
                'box_model' => $box['model_name'],
                'length' => round(wc_get_dimension($box['length'], $output_dimension_unit, $dimension_unit),0,PHP_ROUND_HALF_EVEN),
                'width' => round(wc_get_dimension($box['width'], $output_dimension_unit, $dimension_unit),0,PHP_ROUND_HALF_EVEN),
                'height' => round(wc_get_dimension($box['height'], $output_dimension_unit, $dimension_unit),0,PHP_ROUND_HALF_EVEN),
                'weight' => 0,
                'max_weight' => wc_get_weight($box['max_weight'], $output_weight_unit, $weight_unit),
            ];
            $packageBox = self::addOptionalValues($packageBox, $box);
            $packageBoxes[] = $packageBox;
        }


        return $packageBoxes;
    }
}

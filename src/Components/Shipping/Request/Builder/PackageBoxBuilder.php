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

    public static function addOptionalValues($packageBox, $boxValue)
    {
        foreach (['inner_length', 'inner_width', 'inner_height', 'weight'] as $key => $value) {
            if (isset($boxValue[$value]) && $boxValue[$value] > 0) {
                $packageBox[$value] = $boxValue[$value];
            }
        }

        return $packageBox;
    }

    public function build($payload = null)
    {
        $boxes = $payload['options']->get('package_box');

        $packageBoxes = [];

        foreach ($boxes as $box) {
            $packageBox = [
                'box_model' => $box['model_name'],
                'length' => $box['length'],
                'width' => $box['width'],
                'height' => $box['height'],
                'weight' => 0,
                'max_weight' => $box['max_weight'],
            ];
            $packageBox = self::addOptionalValues($packageBox, $box);
            $packageBoxes[] = $packageBox;
        }

        return $packageBoxes;
    }
}

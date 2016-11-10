<?php

namespace FS\Configurations\WordPress\RequestBuilder;

class PackageBoxBuilder extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    public function build($payload = null)
    {
        $boxes = $payload['options']->get('package_box');

        $packageBoxes = array();

        foreach ($boxes as $box) {
            $packageBoxes[] = array(
                'box_model' => $box['model_name'],
                'length' => $box['length'],
                'width' => $box['width'],
                'height' => $box['height'],
                'weight' => $box['weight'],
                'max_weight' => $box['max_weight'],
            );
        }

        return $packageBoxes;
    }
}

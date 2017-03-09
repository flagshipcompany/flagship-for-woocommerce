<?php

namespace FS\Configurations\WordPress\RequestBuilder;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\RequestBuilder\RequestBuilderInterface;

class ProductItemBuilder extends AbstractComponent implements RequestBuilderInterface
{
    public function build($payload = null)
    {
        $items = array();

        foreach ($payload['productItems'] as $productItem) {
            $items[] = array(
                'length' => $productItem['length'],
                'width' => $productItem['width'],
                'height' => $productItem['height'],
                'weight' => $productItem['weight'],
                'description' => $productItem['id'],
            );
        }

        return $items;
    }
}

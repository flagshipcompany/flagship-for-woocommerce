<?php

namespace FS\Configurations\WordPress\RequestBuilder;

class ProductItemBuilder extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
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

<?php

namespace FS\Components\Shipping\Request\Builder;

use FS\Components\AbstractComponent;

class ProductItemBuilder extends AbstractComponent implements BuilderInterface
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

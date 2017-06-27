<?php

namespace FS\Components\Shipping\Request\Builder;

use FS\Components\AbstractComponent;

class ProductItemBuilder extends AbstractComponent implements BuilderInterface
{
    public function build($payload = null)
    {
        $items = [];

        foreach ($payload['productItems'] as $productItem) {
            $items[] = [
                'length' => $productItem['length'],
                'width' => $productItem['width'],
                'height' => $productItem['height'],
                'weight' => $productItem['weight'],
                'description' => isset($productItem['id']) ? $productItem['id'] : '',
            ];
        }

        return $items;
    }
}

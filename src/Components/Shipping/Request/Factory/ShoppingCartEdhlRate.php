<?php

namespace FS\Components\Shipping\Request\Factory;

use FS\Components\Shipping\Request\Builder\BuilderInterface;
use FS\Components\Shipping\Request\Builder\AbstractPackageItemsBuilder;

class ShoppingCartEdhlRate extends ShoppingCartRate
{
    protected function makeRequestPart(BuilderInterface $builder, $data)
    {
        $requestPart = parent::makeRequestPart($builder, $data);

        if ($builder instanceof AbstractPackageItemsBuilder) {
            $requestPart = $builder->convertPackageUnitsForEdhl($requestPart);
        }

        return $requestPart;
    }
}

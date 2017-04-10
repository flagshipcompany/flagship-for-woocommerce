<?php

namespace FS\Components\Shipping\Request\Builder\Order\PackageItems;

use FS\Components\Shipping\Request\Builder\AbstractPackageItemsBuilder;
use FS\Components\Shipping\Request\Builder\BuilderInterface;

class FallbackBuilder extends AbstractPackageItemsBuilder implements BuilderInterface
{
    protected function makeProductItems($payload = null)
    {
        $order = $payload['shipping']->getOrder();

        $orderItems = $order->native()->get_items();
        $productItems = array();

        foreach ($orderItems as $orderItem) {
            $product = $order->native()->get_product_from_item($orderItem);

            $count = 0;

            list(
                $width,
                $length,
                $height,
                $weight,
                $id
            ) = $this->getProductDimensions($product);

            do {
                $productItems[] = array(
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    'weight' => $weight,
                    'id' => $id,
                );

                ++$count;
            } while ($count < $orderItem['qty']);
        }

        return $productItems;
    }
}

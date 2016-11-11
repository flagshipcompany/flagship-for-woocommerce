<?php

namespace FS\Configurations\WordPress\RequestBuilder\Order\PackageItems;

class FallbackBuilder extends \FS\Configurations\WordPress\RequestBuilder\AbstractPackageItemsBuilder implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    protected function makeProductItems($payload = null)
    {
        $orderItems = $payload['order']->getNativeOrder()->get_items();
        $productItems = array();

        foreach ($orderItems as $orderItem) {
            $product = $payload['order']->getNativeOrder()->get_product_from_item($orderItem);

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

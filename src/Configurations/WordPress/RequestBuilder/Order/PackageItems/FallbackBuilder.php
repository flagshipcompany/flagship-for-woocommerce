<?php

namespace FS\Configurations\WordPress\RequestBuilder\Order\PackageItems;

class FallbackBuilder extends \FS\Configurations\WordPress\RequestBuilder\AbstractPackageItemsBuilder implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    protected function makeProductItems($payload = null)
    {
        $order_items = $payload['order']->getNativeOrder()->get_items();
        $product_items = array();

        foreach ($order_items as $order_item) {
            $product = $payload['order']->getNativeOrder()->get_product_from_item($order_item);

            $count = 0;

            list(
                $width,
                $length,
                $height,
                $weight
            ) = $this->getProductDimensions($product);

            do {
                $product_items[] = array(
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    'weight' => $weight,
                );

                ++$count;
            } while ($count < $order_item['qty']);
        }

        return $product_items;
    }
}

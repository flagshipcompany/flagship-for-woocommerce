<?php

namespace FS\Components\Shipping\Request\Builder\Cart\PackageItems;

use FS\Components\Shipping\Request\Builder\AbstractPackageItemsBuilder;
use FS\Components\Shipping\Request\Builder\BuilderInterface;
use FS\Components\Alert\Notifier;

class FallbackBuilder extends AbstractPackageItemsBuilder implements BuilderInterface
{
    protected function makeProductItems($payload)
    {
        $product_items = array();

        $notifier = $payload['notifier'];
        $notifier->scenario(Notifier::SCOPE_CART);

        if ($payload['options']->eq('disable_api_warning', 'yes')) {
            $notifier->getScenario()->enableSilentLogging();
        }

        foreach ($payload['package']['contents'] as $id => $item) {
            if (!$item['data']->needs_shipping()) {
                continue;
            }

            if (!$item['data']->get_weight()) {
                $notifier->notice('Product %s is missing weight, weight default to 1 lbs.', [$item['data']->get_title()]);
            }

            $count = 0;

            list(
                $width,
                $length,
                $height,
                $weight,
                $skuNo
            ) = $this->getProductDimensions($item['data']);

            do {
                $product_items[] = apply_filters( 'flagship_cart_item_product_dimensions', array(
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    'weight' => $weight,
                    'sku_no' => $skuNo,
                    'shipping_class' => $item['data']->get_shipping_class(),
                ), $item );

                ++$count;
            } while ($count < $item['quantity']);
        }

        return apply_filters( 'flagship_cart_product_items', $product_items );
    }
}

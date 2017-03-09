<?php

namespace FS\Configurations\WordPress\RequestBuilder\Cart\PackageItems;

use FS\Configurations\WordPress\RequestBuilder\AbstractPackageItemsBuilder;
use FS\Components\Shipping\RequestBuilder\RequestBuilderInterface;

class FallbackBuilder extends AbstractPackageItemsBuilder implements RequestBuilderInterface
{
    protected function makeProductItems($payload)
    {
        $product_items = array();

        $notifier = $payload['notifier'];
        $notifier->scope('cart');

        if ($payload['options']->get('disable_api_warning') == 'yes') {
            $notifier->enableSilentLogging();
        }

        foreach ($payload['package']['contents'] as $id => $item) {
            if (!$item['data']->needs_shipping()) {
                continue;
            }

            if (!$item['data']->get_weight()) {
                $notifier->notice(sprintf(__('Product %s is missing weight, weight default to 1 lbs.', FLAGSHIP_SHIPPING_TEXT_DOMAIN), $item['data']->get_title()));
            }

            $count = 0;

            list(
                $width,
                $length,
                $height,
                $weight
            ) = $this->getProductDimensions($item['data']);

            do {
                $product_items[] = array(
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    'weight' => $weight,
                );

                ++$count;
            } while ($count < $item['quantity']);
        }

        return $product_items;
    }
}

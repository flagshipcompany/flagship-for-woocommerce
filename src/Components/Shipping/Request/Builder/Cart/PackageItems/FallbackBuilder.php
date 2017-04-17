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

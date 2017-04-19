<?php

namespace FS\Components\Shipping\Request\Builder\Cart\PackageItems;

use FS\Components\Shipping\Request\Builder\BuilderInterface;

class ApiBuilder extends FallbackBuilder implements BuilderInterface
{
    public function makePackageItems($productItems, $payload)
    {
        $context = $this->getApplicationContext();

        $options = $context->option();
        $client = $context->api();
        $command = $context->command();
        $notifier = $context->alert();

        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderPacking');

        $response = $command->pack(
            $client,
            $factory->setPayload(array(
                'options' => $options,
                'productItems' => $productItems,
            ))->getRequest()
        );

        // when failed, we need to use fallback
        if (!$response->isSuccessful()) {
            return parent::makePackageItems($productItems, $payload);
        }

        $body = $response->getContent();
        $items = array();

        foreach ($body['packages'] as $package) {
            $items[] = array(
                'length' => $package['length'],
                'width' => $package['width'],
                'height' => $package['height'],
                'weight' => $package['weight'],
                'description' => 'product: '.implode(', ', $package['items']),
            );
        }

        return $items;
    }
}

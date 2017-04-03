<?php

namespace FS\Components\Shipping\RequestBuilder\Cart\PackageItems;

use FS\Components\Shipping\RequestBuilder\RequestBuilderInterface;

class ApiBuilder extends FallbackBuilder implements RequestBuilderInterface
{
    public function makePackageItems($productItems, $payload)
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');
        $settings = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Settings');
        $client = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Http\\Client');
        $command = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Command');
        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier');
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderPackingRequestFactory');

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
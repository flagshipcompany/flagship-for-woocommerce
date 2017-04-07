<?php

namespace FS\Components\Shipping\Request\Builder\Order\PackageItems;

use FS\Components\Shipping\Request\Builder\BuilderInterface;

class ApiBuilder extends FallbackBuilder implements BuilderInterface
{
    public function makePackageItems($productItems, $payload)
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');
        $client = $this->getApplicationContext()->api();
        $command = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Command');
        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier');
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderPacking');

        $response = $command->pack(
            $client,
            $factory->setPayload(array(
                'options' => $options,
                'productItems' => $productItems,
            ))->getRequest()
        );

        // when failed, we need to use fallback
        if (!$response->isSuccessful()) {
            $notifier->warning('Unable to use FlagShip Packing API. Use fallback weight driven packing.');

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

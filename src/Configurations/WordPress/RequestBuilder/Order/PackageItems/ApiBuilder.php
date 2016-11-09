<?php

namespace FS\Configurations\WordPress\RequestBuilder\Order\PackageItems;

class ApiBuilder extends FallbackBuilder implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    public function build($payload = null)
    {
        $packages = array(
            'items' => $this->makePackageItems($this->makeProductItems($payload), $payload),
            'units' => 'imperial',
            'type' => 'package',
        );

        return $packages;
    }

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
                'order' => $order,
                'options' => $options,
            ))->getRequest()
        );
    }
}

<?php

namespace FS\Components\Shipping\Factory;

class ShoppingOrderConfirmationRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
{
    public function makeRequest(FormattedRequestInterface $request)
    {
        $request->setRequestPart(
            'from',
            $this->makeRequestPart(
                new \FS\Components\Shipping\RequestBuilder\ShipperAddressBuilder(),
                $this->payload['options']
            )
        );

        $toAddress = $this->makeRequestPart(
            new \FS\Components\Shipping\RequestBuilder\ShoppingOrder\ReceiverAddressBuilder(),
            $this->payload
        );

        $request->setRequestPart(
            'to',
            $toAddress
        );

        $request->setRequestPart(
            'packages',
            $this->makeRequestPart(
                $this->getApplicationContext()->getComponent('\\FS\\Components\\Shipping\\RequestBuilder\\ShoppingOrder\\PackageItemsBuilder'),
                $this->payload
            )
        );

        $request->setRequestPart(
            'payment',
            array(
                'payer' => 'F',
            )
        );

        $request->setRequestPart(
            'service',
            $this->makeRequestPart(
                $this->getApplicationContext()->getComponent('\\FS\\Components\\Shipping\\RequestBuilder\\ShoppingOrder\\ShippingServiceBuilder'),
                $this->payload
            )
        );

        $request->setRequestPart(
            'options',
            $this->makeRequestPart(
                $this->getApplicationContext()->getComponent('\\FS\\Components\\Shipping\\RequestBuilder\\ShoppingOrder\\ShippingOptionsBuilder'),
                $this->payload + $request->getRequest()
            )
        );

        return $request;
    }
}

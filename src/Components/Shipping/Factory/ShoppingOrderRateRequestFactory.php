<?php

namespace FS\Components\Shipping\Factory;

class ShoppingOrderRateRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
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

        // validate north american address
        if (in_array($toAddress['country'], array('CA', 'US'))) {
            $request->setRequestpart(
                'options',
                array(
                    'address_correction' => true,
                )
            );
        }

        return $request;
    }
}

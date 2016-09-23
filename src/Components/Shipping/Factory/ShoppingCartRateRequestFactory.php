<?php

namespace FS\Components\Shipping\Factory;

class ShoppingCartRateRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
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
            new \FS\Components\Shipping\RequestBuilder\ShoppingCart\ReceiverAddressBuilder(),
            $this->payload['package']
        );

        $request->setRequestPart(
            'to',
            $toAddress
        );

        $request->setRequestPart(
            'packages',
            $this->makeRequestPart(
                $this->ctx->getComponent('\\FS\\Components\\Shipping\\RequestBuilder\\ShoppingCart\\PackageItemsBuilder'),
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

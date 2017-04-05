<?php

namespace FS\Components\Shipping\Factory;

use FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory;

class ShoppingOrderRateRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
{
    public function makeRequest(FormattedRequestInterface $request, RequestBuilderFactory $factory)
    {
        $request->add(
            'from',
            $this->makeRequestPart(
                $factory->resolve('ShipperAddress', [
                    'type' => 'order',
                ]),
                $this->payload
            )
        );

        $toAddress = $this->makeRequestPart(
            $factory->resolve('ReceiverAddress', array(
                'type' => 'order',
            )),
            $this->payload
        );

        $request->add(
            'to',
            $toAddress
        );

        $request->add(
            'packages',
            $this->makeRequestPart(
                $factory->resolve('PackageItems', array(
                    'type' => 'order',
                    'usePackingApi' => $this->payload['options']->eq('enable_packing_api', 'yes'),
                )),
                $this->payload
            )
        );

        $request->add(
            'payment',
            array(
                'payer' => 'F',
            )
        );

        // validate north american address
        if (in_array($toAddress['country'], array('CA', 'US'))) {
            $request->add(
                'options',
                array(
                    'address_correction' => true,
                )
            );
        }

        return $request;
    }
}

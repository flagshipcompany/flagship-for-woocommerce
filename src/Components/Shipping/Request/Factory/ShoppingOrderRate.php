<?php

namespace FS\Components\Shipping\Request\Factory;

use FS\Components\Shipping\Request\Builder\Factory\RequestBuilderFactory;
use FS\Components\Shipping\Request\FormattedRequestInterface;

class ShoppingOrderRate extends AbstractRequestFactory
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
                    'usePackingApi' =>  $this->payload['options']->eq('default_package_box_split', 'packing'),
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

        $request->add(
            'options',
            $this->makeRequestPart(
                $factory->resolve('ShippingOptions', array(
                    'type' => 'order',
                )),
                $this->payload + $request->getRequest()
            )
        );

        return $request;
    }
}

<?php

namespace FS\Components\Shipping\Request\Factory;

use FS\Components\Shipping\Request\Builder\Factory\RequestBuilderFactory;
use FS\Components\Shipping\Request\FormattedRequestInterface;

class ShoppingOrderConfirmation extends AbstractRequestFactory
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

        $request->add(
            'service',
            $this->makeRequestPart(
                $factory->resolve('ShippingService', [
                    'type' => 'order',
                ]),
                $this->payload
            )
        );

        $request->add(
            'options',
            $this->makeRequestPart(
                $factory->resolve('ShippingOptions', array(
                    'type' => 'order',
                )),
                $this->payload + $request->getRequest()
            )
        );

        if ($toAddress['country'] == 'CA') {
            return $request;
        }

        // build commercial invoice
        $ci = $this->makeRequestPart(
            $factory->resolve('CommercialInvoice', array(
                'type' => 'order',
            )),
            $this->payload + $request->getRequest()
        );

        $request->add('sold_to', $ci['sold_to']);
        $request->add('inquiry', $ci['inquiry']);
        $request->add('declared_items', $ci['declared_items']);

        return $request;
    }
}

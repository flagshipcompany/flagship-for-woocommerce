<?php

namespace FS\Components\Shipping\Factory;

use FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory;

class ShoppingOrderPickupRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
{
    public function makeRequest(FormattedRequestInterface $request, RequestBuilderFactory $factory)
    {
        $request->add(
            'address',
            $this->makeRequestPart(
                $factory->resolve('ShipperAddress', [
                    'type' => 'order',
                ]),
                $this->payload
            )
        );

        $request->add(
            'courier',
            strtolower($this->payload['shipment']['service']['courier_name'])
        );

        $request->add(
            'boxes',
            count($this->payload['shipment']['packages'])
        );

        $request->add(
            'weight',
            array_reduce($this->payload['shipment']['packages'], function ($carry, $package) {
                $carry += $package['weight'];

                return $carry;
            }, 0)
        );

        $request->add(
            'date',
            $this->payload['date']
        );

        $request->add(
            'from',
            $this->payload['options']->get('default_pickup_time_from', '09:00')
        );

        $request->add(
            'until',
            $this->payload['options']->get('default_pickup_time_to', '17:00')
        );

        $request->add(
            'units',
            'imperial'
        );

        $request->add(
            'location',
            'Reception'
        );

        $request->add(
            'to_country',
            $this->payload['order']->getNativeOrder()->shipping_country
        );

        $request->add(
            'is_ground',
            (strtolower($this->payload['shipment']['service']['courier_name']) == 'fedex' && strpos($this->payload['shipment']['service']['courier_code'], 'FedexGround') !== false)
        );

        return $request;
    }
}

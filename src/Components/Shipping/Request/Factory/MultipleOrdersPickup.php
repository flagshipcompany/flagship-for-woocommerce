<?php

namespace FS\Components\Shipping\Request\Factory;

use FS\Components\Shipping\Request\Builder\Factory\RequestBuilderFactory;
use FS\Components\Shipping\Request\FormattedRequestInterface;

class MultipleOrdersPickup extends AbstractRequestFactory
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
            strtolower($this->payload['courier'])
        );

        $request->add(
            'boxes',
            array_reduce($this->payload['shippings'], function ($carry, $shipping) {
                $shipment = $shipping->getShipment();

                if (!$shipment->isCreated()) {
                    return $carry;
                }

                $carry += count($shipment->get('packages'));

                return $carry;
            }, 0)
        );

        $request->add(
            'weight',
            array_reduce($this->payload['shippings'], function ($carry, $shipping) {
                $shipment = $shipping->getShipment();

                if (!$shipment->isCreated()) {
                    return $carry;
                }

                $carry += array_reduce($shipment->get('packages'), function ($weight, $package) {
                    $weight += $package['weight'];

                    return $weight;
                }, 0);

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
            $this->payload['shippings'][0]->getOrder()->native('shipping_country')
        );

        $request->add(
            'is_ground',
            $this->payload['type'] == 'domestic_ground' || $this->payload['type'] == 'international_ground'
        );

        return $request;
    }
}

<?php

namespace FS\Components\Shipping\Request\Factory;

use FS\Components\Shipping\Request\Builder\Factory\RequestBuilderFactory;
use FS\Components\Shipping\Request\FormattedRequestInterface;

class ShoppingOrderPickup extends AbstractRequestFactory
{
    public function makeRequest(FormattedRequestInterface $request, RequestBuilderFactory $factory)
    {
        $shipment = $this->payload['shipping']->getShipment();
        $order = $this->payload['shipping']->getOrder();

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
            $shipment->getCourier()
        );

        $request->add(
            'boxes',
            count($shipment->get('packages'))
        );

        $request->add(
            'weight',
            array_reduce($shipment->get('packages'), function ($carry, $package) {
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
            $order->native('shipping_country')
        );

        $request->add(
            'is_ground',
            $shipment->isFedexGround()
        );

        return $request;
    }
}

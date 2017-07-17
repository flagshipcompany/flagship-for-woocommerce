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
            'shipments',
            [$shipment->getId()]
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
            'location',
            'Reception'
        );

        return $request;
    }
}

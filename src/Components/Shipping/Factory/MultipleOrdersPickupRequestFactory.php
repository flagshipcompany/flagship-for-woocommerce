<?php

namespace FS\Components\Shipping\Factory;

class MultipleOrdersPickupRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
{
    public function makeRequest(FormattedRequestInterface $request, \FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory $factory)
    {
        $request->setRequestPart(
            'address',
            $this->makeRequestPart(
                $factory->getBuilder('ShipperAddress', array(
                    'type' => 'order',
                )),
                $this->payload
            )
        );

        $request->setRequestPart(
            'courier',
            strtolower($this->payload['courier'])
        );

        $request->setRequestPart(
            'boxes',
            array_reduce($this->payload['orders'], function ($carry, $order) {
                $shipment = $order->getFlagShipRaw();

                if (!$shipment) {
                    return $carry;
                }

                $carry += count($shipment['packages']);

                return $carry;
            }, 0)
        );

        $request->setRequestPart(
            'weight',
            array_reduce($this->payload['orders'], function ($carry, $order) {
                $shipment = $order->getFlagShipRaw();

                if (!$shipment) {
                    return $carry;
                }

                $carry += array_reduce($shipment['packages'], function ($weight, $package) {
                    $weight += $package['weight'];

                    return $weight;
                }, 0);

                return $carry;
            }, 0)
        );

        $request->setRequestPart(
            'date',
            $this->payload['date']
        );

        $request->setRequestPart(
            'from',
            $this->payload['options']->get('default_pickup_time_from', '09:00')
        );

        $request->setRequestPart(
            'until',
            $this->payload['options']->get('default_pickup_time_to', '17:00')
        );

        $request->setRequestPart(
            'units',
            'imperial'
        );

        $request->setRequestPart(
            'location',
            'Reception'
        );

        $request->setRequestPart(
            'to_country',
            $this->payload['orders'][0]->getWcOrder()->shipping_country
        );

        $request->setRequestPart(
            'is_ground',
            $this->payload['type'] == 'domestic_ground' ||  $this->payload['type'] == 'international_ground'
        );

        return $request;
    }
}

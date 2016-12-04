<?php

namespace FS\Components\Shipping\Factory;

class ShoppingOrderPickupRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
{
    public function makeRequest(FormattedRequestInterface $request, \FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory $factory)
    {
        $request->setRequestPart(
            'address',
            $this->makeRequestPart(
                $factory->getShipperAddressBuilder(array(
                    'type' => 'order',
                )),
                $this->payload
            )
        );

        $request->setRequestPart(
            'courier',
            strtolower($this->payload['shipment']['service']['courier_name'])
        );

        $request->setRequestPart(
            'boxes',
            count($this->payload['shipment']['packages'])
        );

        $request->setRequestPart(
            'weight',
            array_reduce($this->payload['shipment']['packages'], function ($carry, $package) {
                $carry += $package['weight'];

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
            $this->payload['order']->getNativeOrder()->shipping_country
        );

        $request->setRequestPart(
            'is_ground',
            (strtolower($this->payload['shipment']['service']['courier_name']) == 'fedex' && strpos($this->payload['shipment']['service']['courier_code'], 'FedexGround') !== false)
        );

        return $request;
    }
}

<?php

namespace FS\Components\Shipping\Factory;

use FS\Components\AbstractComponent;

class RegroupShippingsFactory extends AbstractComponent
{
    public function getRegroupedShippings($collection)
    {
        $flattenOrderShippings = [];
        $grouped = $this->groupByCourierType($collection);

        foreach ($grouped as $courier => $shippings) {
            $shippings = array_filter($shippings);

            foreach ($shippings as $type => $sameTypeShippings) {
                $sameTypeShippings = array_filter($sameTypeShippings);

                $flattenOrderShippings[] = array(
                    'courier' => $courier,
                    'type' => $type,
                    'shippings' => $sameTypeShippings,
                    'ids' => array_reduce($sameTypeShippings, function ($ids, $shipping) {
                        $ids[] = $shipping->getOrder()->getId();

                        return $ids;
                    }, []),
                );
            }
        }

        return $flattenOrderShippings;
    }

    public function groupByCourierType($shippings)
    {
        $data = [
            'fedex' => [
                'domestic' => [],
                'domestic_ground' => [],
                'international' => [],
                'international_ground' => [],
            ],
            'purolator' => [
                'normal' => [],
            ],
            'ups' => [
                'domestic' => [],
                'international' => [],
            ],
        ];

        foreach ($shippings as $shipping) {
            $shipment = $shipping->getShipment();

            if (!$shipment->isCreated()) {
                continue;
            }

            $courier = $shipment->getCourier();
            $isInternational = $shipment->isInternational();
            $isFedexGround = $shipment->isFedexGround();

            if ($courier != 'fedex' && $courier != 'ups') {
                $data[$courier]['normal'][] = $shipping;

                continue;
            }

            $type = $isInternational ? 'international' : 'domestic';

            if ($courier == 'ups') {
                $data[$courier][$type][] = $shipping;

                continue;
            }

            $type = $isFedexGround ? $type.'_ground' : $type;

            $data[$courier][$type][] = $shipping;
        }

        return $data;
    }
}

<?php

namespace FS\Components\Order\Factory;

class FlattenOrderShippingsFactory extends \FS\Components\AbstractComponent
{
    public function getFlattenOrderShippings($orders)
    {
        $flattenOrderShippings = array();
        $orderShipppings = $this->getOrderShippings($orders);

        foreach ($orderShipppings as $courier => $shippings) {
            $filteredShippings = array_filter($shippings);

            if (!$filteredShippings) {
                continue;
            }

            foreach ($shippings as $type => $shippingOrders) {
                $filteredOrders = array_filter($shippingOrders);

                if (!$filteredOrders) {
                    continue;
                }

                $flattenOrderShippings[] = array(
                    'courier' => $courier,
                    'type' => $type,
                    'orders' => $filteredOrders,
                    'ids' => array_reduce($filteredOrders, function ($ids, $order) {
                        $ids[] = $order->getId();

                        return $ids;
                    }, array()),
                );
            }
        }

        return $flattenOrderShippings;
    }

    public function getOrderShippings($orders)
    {
        $data = array(
            'fedex' => array(
                'domestic' => array(),
                'domestic_ground' => array(),
                'international' => array(),
                'international_ground' => array(),
            ),
            'purolator' => array(
                'normal' => array(),
            ),
            'ups' => array(
                'domestic' => array(),
                'international' => array(),
            ),
        );

        foreach ($orders as $order) {
            $shipment = $order->getShipment();

            if (!$shipment) {
                continue;
            }

            $courier = $shipment->getCourier();
            $isInternational = $order->isInternational();
            $isFedexGround = $shipment->isFedexGround();

            if ($courier != 'fedex' && $courier != 'ups') {
                $data[$courier]['normal'][] = $order;

                continue;
            }

            $type = $isInternatioanl ? 'international' : 'domestic';

            if ($courier == 'ups') {
                $data[$courier][$type][] = $order;

                continue;
            }

            $type = $isFedexGround ? $type.'_ground' : $type;

            $data[$courier][$type][] = $order;
        }

        return $data;
    }
}

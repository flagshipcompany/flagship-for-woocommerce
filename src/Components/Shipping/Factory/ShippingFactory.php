<?php

namespace FS\Components\Shipping\Factory;

use FS\Components\Shipping\Object\Order;
use FS\Components\Shipping\Object\Shipment;
use FS\Components\Shipping\Object\Shipping;
use FS\Components\Shipping\Object\Pickup;
use FS\Context\Factory\AbstractFactory;

class ShippingFactory extends AbstractFactory
{
    const RESOURCE_ORDER = 'order';
    const RESOURCE_ORDER_COLLECTION = 'collection';
    const RESOURCE_SHIPPING = 'shipping';
    const RESOURCE_PICKUP = 'pickup';

    public function resolve($resource, array $option = [])
    {
        $model = $this->resolveWithoutContext($resource, $option);

        if ($model && is_array($model)) {
            foreach ($model as $m) {
                $m->setApplicationContext($this->getApplicationContext());
            }

            return $model;
        }

        if ($model) {
            return $model->setApplicationContext($this->getApplicationContext());
        }

        throw new \Exception('Unable to resolve shop order: '.$resource, 500);
    }

    public function resolveWithoutContext($resource, array $option = [])
    {
        switch ($resource) {
            case self::RESOURCE_ORDER:
                $order = new Order();

                if (!isset($option['id'])) {
                    throw new \Exception('Unable to retrieve WooCommerce Order');
                }

                $nativeOrder = \wc_get_order($option['id']);

                if (!$nativeOrder) {
                    throw new \Exception('Unable to retrieve WooCommerce Order');
                }

                return $order->setNativeOrder($nativeOrder);
                // no break
            case self::RESOURCE_ORDER_COLLECTION:
                $orders = array();

                if (isset($option['ids'])) {
                    foreach ($option['ids'] as $id) {
                        $orders[] = $this->resolve(self::RESOURCE_ORDER, array(
                            'id' => $id,
                        ));
                    }

                    return $orders;
                }

                throw new \Exception('Unable to retieve WooCommerce Orders');
                // no break
            case self::RESOURCE_SHIPPING:
                $order = self::resolveWithoutContext(self::RESOURCE_ORDER, $option);
                $shipment = new Shipment();

                $shipment->syncWithOrder($order);

                $pickup = new Pickup();

                $pickup->syncWithOrder($order);

                $shipping = new Shipping();

                $shipping->setOrder($order);
                $shipping->setShipment($shipment);
                $shipping->setPickup($pickup);

                return $shipping;
                // no break
        }
    }
}

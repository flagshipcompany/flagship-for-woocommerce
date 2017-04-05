<?php

namespace FS\Components\Shop\Factory;

use FS\Components\Shop\Order;
use FS\Components\Shop\Shipment;
use FS\Components\AbstractComponent;
use FS\Context\Factory\FactoryInterface;

class ShopFactory extends AbstractComponent implements FactoryInterface
{
    const RESOURCE_ORDER = 'order';
    const RESOURCE_SHIPMENT = 'shipment';
    const RESOURCE_ORDER_COLLECTION = 'collection';

    public function resolve($resource, $option = [])
    {
        $model = $this->resolveModel($resource, $option);

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

    protected function resolveModel($resource, $option = [])
    {
        switch ($resource) {
            case self::RESOURCE_ORDER:
                $order = new Order();

                if (isset($option['nativeOrder']) && $option['nativeOrder'] instanceof \WC_Order) {
                    return $order->setNativeOrder($option['nativeOrder']);
                }

                if (isset($option['id']) && $wcOrder = \wc_get_order($option['id'])) {
                    return $order->setNativeOrder($wcOrder);
                }

                throw new \Exception('Unable to retieve WooCommerce Order');
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
            case self::RESOURCE_SHIPMENT:
                if ($option['raw']) {
                    $shipment = new Shipment();

                    foreach ($option['raw'] as $key => $value) {
                        $shipment[$key] = $value;
                    }

                    return $shipment;
                }

                throw new \Exception('Unable to retieve FlagShip shipment data');
                // no break
        }
    }
}

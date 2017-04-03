<?php

namespace FS\Components\Shop\Factory;

use FS\Components\Shop\Order;
use FS\Components\Shop\Shipment;
use FS\Components\AbstractComponent;

class ShopFactory extends AbstractComponent implements FactoryInterface
{
    public function getModel($resource, $context = array())
    {
        $model = $this->resolveModel($resource, $context);

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

    protected function resolveModel($resource, $context = [])
    {
        switch ($resource) {
            case self::RESOURCE_ORDER:
                $order = new Order();

                if (isset($context['nativeOrder']) && $context['nativeOrder'] instanceof \WC_Order) {
                    return $order->setNativeOrder($context['nativeOrder']);
                }

                if (isset($context['id']) && $wcOrder = \wc_get_order($context['id'])) {
                    return $order->setNativeOrder($wcOrder);
                }

                throw new \Exception('Unable to retieve WooCommerce Order');
                // no break
            case self::RESOURCE_ORDER_COLLECTION:
                $orders = array();

                if (isset($context['ids'])) {
                    foreach ($context['ids'] as $id) {
                        $orders[] = $this->getModel(self::RESOURCE_ORDER, array(
                            'id' => $id,
                        ));
                    }

                    return $orders;
                }

                throw new \Exception('Unable to retieve WooCommerce Orders');
                // no break
            case self::RESOURCE_SHIPMENT:
                if ($context['raw']) {
                    $shipment = new Shipment();

                    foreach ($context['raw'] as $key => $value) {
                        $shipment[$key] = $value;
                    }

                    return $shipment;
                }

                throw new \Exception('Unable to retieve FlagShip shipment data');
                // no break
        }
    }
}

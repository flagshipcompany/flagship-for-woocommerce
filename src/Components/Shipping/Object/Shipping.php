<?php

namespace FS\Components\Shipping\Object;

use FS\Components\AbstractComponent;

class Shipping extends AbstractComponent
{
    protected $shipment;
    protected $order;

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    public function getShipment()
    {
        return $this->shipment;
    }

    public function setShipment($shipment)
    {
        $this->shipment = $shipment;
    }

    public function getService($phrase = null)
    {
        if (!$phrase) {
            $methods = $this->order->native()->get_shipping_methods();
            $phrase = $methods[key($methods)]['method_id'];
        }

        return self::parseServicePhrase($phrase);
    }

    public function isFlagShipRateChoosen()
    {
        $service = $this->getService();
        $context = $this->getApplicationContext();

        return $service['provider'] == $context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID');
    }

    // static methods
    public static function parseServicePhrase($phrase)
    {
        $methodsArray = explode('|', $phrase);
        $instanceId;

        if (count($methodsArray) == 6) {
            list($provider, $courier_name, $courier_code, $courier_desc, $date, $instanceId) = $methodsArray;
        } else {
            list($provider, $courier_name, $courier_code, $courier_desc, $date) = $methodsArray;
        }

        $service = [
            'provider' => $provider,
            'courier_name' => strtolower($courier_name),
            'courier_code' => $courier_code,
            'courier_desc' => $courier_desc,
            'date' => $date,
            'instance_id' => isset($instanceId) ? $instanceId : 0,
        ];

        return $service;
    }
}

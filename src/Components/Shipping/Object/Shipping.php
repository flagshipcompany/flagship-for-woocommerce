<?php

namespace FS\Components\Shipping\Object;

use FS\Components\AbstractComponent;

class Shipping extends AbstractComponent
{
    protected $shipment;
    protected $order;
    protected $pickup;

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    public function getShipment()
    {
        return $this->shipment;
    }

    public function setShipment(Shipment $shipment)
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function getPickup()
    {
        return $this->pickup;
    }

    public function setPickup(Pickup $pickup)
    {
        $this->pickup = $pickup;

        return $this;
    }

    public function getService($phrase = null)
    {
        if (!$phrase) {
            $methods = $this->order->native()->get_shipping_methods();
            $phrase = $methods[key($methods)]['method_id'];
        }

        return self::parseServicePhrase($phrase);
    }

    public function save($option = [])
    {
        $data = $this->shipment->toArray();

        $this->order->setAttribute('flagship_shipping_raw', $data);

        if (isset($option['save_meta_keys']) && $option['save_meta_keys']) {
            $this->order->setAttribute('flagship_shipping_shipment_id', $data['shipment_id']);
            $this->order->setAttribute('flagship_shipping_shipment_tracking_number', $data['tracking_number']);
            $this->order->setAttribute('flagship_shipping_courier_name', $data['service']['courier_name']);
            $this->order->setAttribute('flagship_shipping_courier_service_code', $data['service']['courier_code']);
        }

        return $this;
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

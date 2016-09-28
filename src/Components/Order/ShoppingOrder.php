<?php

namespace FS\Components\Order;

class ShoppingOrder extends \FS\Components\AbstractComponent implements WcOrderAwareInterface
{
    protected static $scope = 'prototype';

    protected static $domesticCountry = 'CA';

    protected $wcOrder;
    protected $shipment = null;

    public function setWcOrder($wcOrder)
    {
        $this->wcOrder = $wcOrder;
        $this->getShipment(true);

        return $this;
    }

    public function getWcOrder()
    {
        return $this->wcOrder;
    }

    public function getId()
    {
        return $this->wcOrder->id;
    }

    public function getAttribute($key)
    {
        $data = get_post_meta($this->getId(), $key, true);

        return $data;
    }

    public function setAttribute($key, $value)
    {
        // fix double quote slash
        if (is_string($value)) {
            $value = wp_slash($value);
        }

        update_post_meta($this->getId(), $key, $value);

        return $this;
    }

    public function getShipment($forceSync = false)
    {
        if (!$forceSync) {
            return $this->shipment;
        }

        $rawShipment = $this->getFlagShipRaw();

        if (!$rawShipment) {
            return;
        }

        $this->shipment = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\Shipment');
        // $this->shipment = new \FS\Components\Order\Shipment();

        // $this->shipment->setApplicationContext($this->getApplicationContext());
        $this->shipment->setRawShipment($rawShipment);

        return $this->shipment;
    }

    public function isInternational()
    {
        return $this->getWcOrder()->shipping_country != self::$domesticCountry;
    }

    public function deleteAttribute($key)
    {
        delete_post_meta($this->getId(), $key);

        return $this;
    }

    public function getFlagShipRaw($key = 'flagship_shipping_raw')
    {
        return $this->getAttribute($key);
    }

    public function setFlagShipRaw($data)
    {
        $this->setAttribute('flagship_shipping_raw', $data);

        return $this;
    }

    public function getShippingService($serviceString = null)
    {
        if (!$serviceString) {
            $shipping_methods = $this->getWcOrder()->get_shipping_methods();
            $serviceString = $shipping_methods[key($shipping_methods)]['method_id'];
        }

        return $this->parseShippingServiceString($serviceString);
    }

    protected function parseShippingServiceString($serviceString)
    {
        $methodsArray = explode('|', $serviceString);
        $instanceId;

        if (count($methodsArray) == 6) {
            list($provider, $courier_name, $courier_code, $courier_desc, $date, $instanceId) = $methodsArray;
        } else {
            list($provider, $courier_name, $courier_code, $courier_desc, $date) = $methodsArray;
        }

        $service = array(
            'provider' => $provider,
            'courier_name' => strtolower($courier_name),
            'courier_code' => $courier_code,
            'courier_desc' => $courier_desc,
            'date' => $date,
            'instance_id' => isset($instanceId) ? $instanceId : 0,
        );

        return $service;
    }
}

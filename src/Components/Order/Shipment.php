<?php

namespace FS\Components\Order;

class Shipment extends \FS\Components\AbstractComponent
{
    protected static $scope = 'prototype';

    protected $rawShipment;
    protected $wcOrderId;

    public function setRawShipment(array $rawShipment = null)
    {
        $this->rawShipment = $rawShipment;

        return $this;
    }

    public function getRawShipment()
    {
        return $this->rawShipment;
    }

    public function getCourier()
    {
        return strtolower($this->rawShipment['service']['courier_name']);
    }

    public function isFedexGround()
    {
        return $this->getCourier() == 'fedex' && (strpos($this->rawShipment['service']['courier_code'], 'FedexGround') !== false);
    }
}

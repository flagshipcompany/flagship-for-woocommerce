<?php

namespace FS\Components\Shop;

use FS\Components\Model\AbstractModel;

class Shipment extends AbstractModel
{
    public function setReceiverAddress($address)
    {
        $this['to'] = $address;

        return $this;
    }

    public function getId()
    {
        return $this['shipment_id'];
    }

    public function getCourier()
    {
        return strtolower($this['service']['courier_name']);
    }

    public function isFedexGround()
    {
        return $this->getCourier() == 'fedex' && (strpos($this['service']['courier_code'], 'FedexGround') !== false);
    }
}

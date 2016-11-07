<?php

namespace FS\Configurations\WordPress\Shop;

class Shipment extends \FS\Container
{
    public static function createFromRaw(array $raw)
    {
        $shipment = new self();

        foreach ($raw as $key => $value) {
            $shipment[$key] = $value;
        }

        return $shipment;
    }

    public function setReceiverAddress($address)
    {
        $this['to'] = $address;

        return $this;
    }
}

<?php

namespace FS\Components\Shipping\Object;

use FS\Components\AbstractComponent;

class Pickup extends AbstractComponent
{
    use RawDataAccessTrait;

    protected $created = false;
    protected $raw = [];

    public function getId()
    {
        if (!$this->isCreated()) {
            return;
        }

        return $this->raw['id'];
    }

    public function isCreated()
    {
        return $this->created;
    }

    public function syncWithOrder(Order $order)
    {
        $raw = $order->getAttribute('flagship_shipping_raw');
        $raw = $raw ?: [];

        if (!$raw || !isset($raw['pickup'])) {
            $this->created = false;
            $this->raw = $raw;

            return $this;
        }

        $this->created = true;
        $this->raw = $raw['pickup'];

        return $this;
    }
}

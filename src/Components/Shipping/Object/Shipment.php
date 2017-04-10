<?php

namespace FS\Components\Shipping\Object;

class Shipment
{
    const STATUS_PREQUOTED = 1;
    const STATUS_CREATED = 2;

    protected $status = self::STATUS_PREQUOTED;
    protected $raw = [];
    protected $addresses = [];

    public function getId()
    {
        if ($this->isCreated() && isset($this->raw['shipment_id'])) {
            return $this->raw['shipment_id'];
        }
    }

    public function getCourier()
    {
        if (!$this->isCreated() || !isset($this->raw['service']['courier_name'])) {
            return;
        }

        return $this->raw['service']['courier_name'];
    }

    public function getToAddress()
    {
        return $this->addresses['to'];
    }

    public function isPrequoted()
    {
        return $this->status == self::STATUS_PREQUOTED;
    }

    public function isCreated()
    {
        return (bool) $this->raw;
    }

    public function isFedexGround()
    {
        return $this->getCourier() == 'fedex' && (strpos($this->raw['service']['courier_code'], 'FedexGround') !== false);
    }

    public function isInternational()
    {
        return $this->addresses['to']['country'] != 'CA';
    }

    public function hasPickup()
    {
        return isset($this->raw['pickup']) && $this->raw['pickup'];
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function syncWithOrder(Order $order)
    {
        $raw = $order->getAttribute('flagship_shipping_raw');
        $raw = $raw ?: [];

        $this->raw = $raw;

        if ($this->raw) {
            $this->setStatus(self::STATUS_CREATED);
        }

        // make receiver address
        $this->addresses['to'] = [
            'name' => $order->native('shipping_company'),
            'attn' => $order->native('shipping_first_name').' '.$order->native('shipping_last_name'),
            'address' => trim($order->native('shipping_address_1').' '.$order->native('shipping_address_2')),
            'city' => $order->native('shipping_city'),
            'state' => $order->native('shipping_state'),
            'country' => $order->native('shipping_country'),
            'postal_code' => $order->native('shipping_postcode'),
            'phone' => $order->native('billing_phone'), // no such a field in the shipping!?
        ];

        return $this;
    }

    public function toArray()
    {
        return $this->raw;
    }

    public static function createFromOrder(Order $order)
    {
        $shipment = new self();

        return $shipment->syncWithOrder($order);
    }
}

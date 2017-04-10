<?php

namespace FS\Order;

class Shipping
{
    protected $shipment = null;
    protected $order;

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getShipment()
    {
        return $this->shipment;
    }

    public function setShipment($shipment)
    {
        $this->shipment = $shipment;
    }

    public function getToAddress()
    {
        return [
            'name' => $this->order->shipping_company,
            'attn' => $this->order->shipping_first_name.' '.$this->order->shipping_last_name,
            'address' => trim($this->order->shipping_address_1.' '.$this->order->shipping_address_2),
            'city' => $this->order->shipping_city,
            'state' => $this->order->shipping_state,
            'country' => $this->order->shipping_country,
            'postal_code' => $this->order->shipping_postcode,
            'phone' => $this->order->billing_phone, // no such a field in the shipping!?
        ];
    }

    public static function createShippingFromNativeOrder($nativeOrder)
    {
    }
}

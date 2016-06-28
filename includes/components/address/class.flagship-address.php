<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Address extends Flagship_Component
{
    public function get_from()
    {
        $address = array(
            'country' => 'CA',
            'state' => $this->ctx['options']->get('freight_shipper_state'),
            'city' => $this->ctx['options']->get('freight_shipper_city'),
            'postal_code' => $this->ctx['options']->get('origin'),
            'address' => $this->ctx['options']->get('freight_shipper_street'),
            'name' => $this->ctx['options']->get('shipper_company_name'),
            'attn' => $this->ctx['options']->get('shipper_person_name'),
            'phone' => $this->ctx['options']->get('shipper_phone_number'),
            'ext' => $this->ctx['options']->get('shipper_phone_ext'),
        );

        return $address;
    }

    public function get_quote_to($package)
    {
        $address = array(
            'country' => $package['destination']['country'],
            'state' => $package['destination']['state'],
            'city' => $package['destination']['city'],
            'postal_code' => $package['destination']['postcode'],
            'address' => $package['destination']['address'].' '.$package['destination']['address_2'],
        );

        return $address;
    }

    public function has_receiver_address($package)
    {
        return !empty($package['destination']['city']) && !empty($package['destination']['postcode']);
    }

    public function get_order_to($order)
    {
        $address = array(
            'name' => $order->shipping_company,
            'attn' => $order->shipping_first_name.' '.$order->shipping_last_name,
            'address' => trim($order->shipping_address_1.' '.$order->shipping_address_2),
            'city' => $order->shipping_city,
            'state' => $order->shipping_state,
            'country' => $order->shipping_country,
            'postal_code' => $order->shipping_postcode,
            'phone' => $order->billing_phone, // no such a field in the shipping!?
        );

        return $address;
    }
}

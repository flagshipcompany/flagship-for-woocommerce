<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Order extends Flagship_Component
{
    protected $id;
    protected $order;

    // boolean
    public function exist()
    {
        return isset($order);
    }

    public function import($order)
    {
        $this->order = $order;
        $this->id = $this->order->id;

        return $this;
    }

    public function get_meta($key)
    {
        $data = get_post_meta($this->id, $key, true);

        return $data;
    }

    public function set_meta($key, $value)
    {
        // fix double quote slash
        if (is_string($value)) {
            $value = wp_slash($value);
        }

        update_post_meta($this->id, $key, $value);

        return $this;
    }

    public function remove_meta($key)
    {
        delete_post_meta($this->id, $key);

        return $this;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_order()
    {
        return $this->order;
    }

    public function get_shipping_service()
    {
        $shipping_methods = $this->order->get_shipping_methods();

        list($provider, $courier_name, $courier_code, $courier_desc, $date) = explode('|', $shipping_methods[key($shipping_methods)]['method_id']);

        $service = array(
            'provider' => $provider,
            'courier_name' => strtolower($courier_name),
            'courier_code' => $courier_code,
            'courier_desc' => $courier_desc,
            'date' => $date,
        );

        return $service;
    }
}

<?php

namespace FS\Components\Order;

class ShoppingOrder extends \FS\Components\AbstractComponent implements WcOrderAwareInterface
{
    protected $wcOrder;

    public function setWcOrder($wcOrder)
    {
        $this->wcOrder = $wcOrder;

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

    public function get($key)
    {
        $data = get_post_meta($this->getId(), $key, true);

        return $data;
    }

    public function set($key, $value)
    {
        // fix double quote slash
        if (is_string($value)) {
            $value = wp_slash($value);
        }

        update_post_meta($this->getId(), $key, $value);

        return $this;
    }

    public function delete($key)
    {
        delete_post_meta($this->getId(), $key);

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

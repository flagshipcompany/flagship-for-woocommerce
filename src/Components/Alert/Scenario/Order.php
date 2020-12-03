<?php

namespace FS\Components\Alert\Scenario;

use FS\Components\AbstractComponent;

class Order extends AbstractComponent
{
    protected $order = null;

    protected static $scope = 'prototype';

    public function withOption($option = [])
    {
        if (isset($option['order'])) {
            $this->order = $option['order'];
        }

        return $this;
    }

    public function isEmpty()
    {
        $existing = $this->order->getAttribute('flagship_shipping_shop_order_meta_notification');

        return (bool) $existing;
    }

    public function add($type, $message)
    {
        $existing = $this->order->getAttribute('flagship_shipping_shop_order_meta_notification');

        if (!is_array($existing)) {
            $existing = [];
        }

        if (!isset($existing[$type])) {
            $existing[$type] = [];
        }

        $existing[$type][] = $message;

        $this->order->setAttribute('flagship_shipping_shop_order_meta_notification', $existing);

        return $this;
    }

    public function view($viewer)
    {
        $notifications = $this->order->getAttribute('flagship_shipping_shop_order_meta_notification');

        $this->order->removeAttribute('flagship_shipping_shop_order_meta_notification');

        $viewer->notification(['notifications' => $notifications ? $notifications : []]);

        return $this;
    }
}

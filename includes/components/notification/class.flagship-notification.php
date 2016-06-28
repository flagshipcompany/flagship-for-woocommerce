<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Notification extends Flagship_Component
{
    public $notifications = array();
    protected $notice_scope = 'native';
    protected $extras = array();

    public function add($type = 'success', $message)
    {
        if ($this->notice_scope == 'shop_order') {
            return $this->shop_order_add($type, $message);
        }

        return $this->native_add($type, $message);
    }

    public function view()
    {
        if ($this->notice_scope == 'shop_order') {
            return $this->shop_order_view();
        }

        if (!$this->notifications) {
            return;
        }

        $this->ctx['view']->notification(array('notifications' => $this->notifications));

        $this->cleanup();
    }

    public function scope($scope = 'native', $extras = array())
    {
        $this->notice_scope = $scope;
        $this->extras = $extras;

        return $this;
    }

    protected function cleanup()
    {
        $this->notifications = array();

        return $this;
    }

    protected function native_add($type = 'success', $message)
    {
        if (!isset($this->notifications[$type])) {
            $this->notifications[$type] = array();
        }

        while (is_array($message) && $message) {
            $msg = array_shift($message);

            $this->native_add($type, $msg);
        }

        if (is_string($message)) {
            $hash = md5($message);

            $this->notifications[$type][$hash] = $message;
        }

        return $this;
    }

    protected function shop_order_add($type = 'success', $message)
    {
        $existing = get_post_meta($this->extras['id'], 'flagship_shipping_shop_order_meta_notification', true);

        if (!isset($existing[$type])) {
            $existing[$type] = array();
        }

        $existing[$type][] = $message;

        update_post_meta($this->extras['id'], 'flagship_shipping_shop_order_meta_notification', $existing);

        return $this;
    }

    protected function shop_order_view()
    {
        $notifications = get_post_meta($this->extras['id'], 'flagship_shipping_shop_order_meta_notification', true);

        delete_post_meta($this->extras['id'], 'flagship_shipping_shop_order_meta_notification');

        $this->ctx['view']->notification(array('notifications' => $notifications ? $notifications : array()));

        $this->scope();
        $this->cleanup();
    }
}

<?php

class Flagship_Notification
{
    public $notifications;
    protected $notice_scope = 'native';
    protected $extras = array();

    public function __construct()
    {
        $this->notifiactions = array();
    }

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

        Flagship_View::notification(array('notifications' => $this->notifications));
    }

    public function scope($scope = 'native', $extras = array())
    {
        $this->notice_scope = $scope;
        $this->extras = $extras;

        return $this;
    }

    protected function native_add($type = 'success', $message)
    {
        if (!isset($this->notifications[$type])) {
            $this->notifications[$type] = array();
        }

        $this->notifications[$type][] = $message;

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

        Flagship_View::notification(array('notifications' => $notifications ? $notifications : array()));

        $this->scope();
    }
}

<?php

namespace FS\Components;

use FS\Components\Factory\ComponentInitializingInterface;

class Notifier extends AbstractComponent implements ComponentInitializingInterface
{
    public $notifications = array();

    protected $viewer;

    protected $notice_scope = 'native';
    protected $extras = array();
    protected $prev = array();
    protected $silent = false;

    /**
     * load viewer after notifier is instantiated.
     */
    public function afterPropertiesSet()
    {
        $this->setViewer($this->getApplicationContext()->_('\\FS\\Components\\Viewer'));
    }

    public function add($type, $message)
    {
        if ($this->notice_scope == 'shop_order') {
            return $this->shop_order_add($type, $message);
        }

        if ($this->notice_scope == 'cart') {
            return $this->native_add($type, $message);
        }

        return $this->native_add($type, $message);
    }

    public function error($message)
    {
        return $this->add('error', $message);
    }

    public function notice($message)
    {
        return $this->add('notice', $message);
    }

    public function warning($message)
    {
        return $this->add('warning', $message);
    }

    public function view()
    {
        if ($this->notice_scope == 'shop_order') {
            return $this->shop_order_view();
        }

        if ($this->notice_scope == 'cart') {
            return $this->cart_view();
        }

        if (!$this->notifications) {
            return;
        }

        $this->viewer->notification(array('notifications' => $this->notifications));

        if ($this->prev) {
            return $this->restore();
        }

        $this->scope();

        return $this->cleanup();
    }

    public function scope($scope = 'native', $extras = [])
    {
        if (!empty($this->notifications)) {
            $this->prev = array(
                'notifications' => $this->notifications,
                'notice_scope' => $this->notice_scope,
                'extras' => $this->extras,
            );

            $this->cleanup();
        }

        $this->notice_scope = $scope;
        $this->extras = $extras;

        return $this;
    }

    public function reverse_order($type)
    {
        if (isset($this->notifications[$type])) {
            $this->notifications[$type] = array_reverse($this->notifications[$type]);
        }

        return $this;
    }

    public function setViewer(\FS\Components\Viewer $viewer)
    {
        $this->viewer = $viewer;

        return $this;
    }

    /**
     * Silent Logging Mode enable affect customer side.
     * by enable silent logging, customer no longer see the warning.
     * The store owner has logging console to browse 10 latest error/warning messages.
     */
    public function enableSilentLogging()
    {
        $this->silent = true;

        return $this;
    }

    public function isSilenced()
    {
        return $this->silent;
    }

    protected function restore()
    {
        $this->notifications = $this->prev['notifcations'];
        $this->notice_scope = $this->prev['notice_scope'];
        $this->extras = $this->prev['extras'];

        $this->prev = array();

        return $this;
    }

    protected function cleanup()
    {
        $this->notifications = array();

        return $this;
    }

    protected function native_add($type, $message)
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

    protected function shop_order_add($type, $message)
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

        $this->viewer->notification(array('notifications' => $notifications ? $notifications : array()));

        if ($this->prev) {
            return $this->restore();
        }

        $this->scope();

        return $this->cleanup();
    }

    public function cart_view()
    {
        if ($this->isSilenced()) {
            $this->getApplicationContext()->getComponent('\\FS\\Components\\Options')->log($this->notifications);
        } else {
            \wc_clear_notices();

            foreach ($this->notifications as $type => $notifications) {
                if (!$notifications) {
                    continue;
                }

                $html = implode('<br/>', $notifications);
                \wc_add_notice($html, $type);
            }
        }

        if ($this->prev) {
            return $this->restore();
        }

        $this->scope();

        return $this->cleanup();
    }
}

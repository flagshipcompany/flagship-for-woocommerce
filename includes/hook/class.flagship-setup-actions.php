<?php

class Flagship_Setup_Actions extends Flagship_Api_Hooks
{
    protected $type = 'action';

    public function __construct()
    {
        $this->add('woocommerce_shipping_init');
    }

    /**
     * Load shipping method.
     *
     * @param array $methods
     */
    public function woocommerce_shipping_init_action($methods)
    {
        if (!class_exists('Flagship_WC_Shipping_Method')) {
            include_once FLS__PLUGIN_DIR.'includes/class.flagship-wc-shipping-method.php';
        }
    }
}

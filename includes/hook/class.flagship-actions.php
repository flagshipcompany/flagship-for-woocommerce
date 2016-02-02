<?php

class Flagship_Actions extends Flagship_Api_Hooks
{
    public function add($action_name, $optional_method_name = false)
    {
        return $this->add_hook('action', $action_name, $optional_method_name);
    }

    // built-in actions
    public static function woocommerce_shipping_init_action($methods)
    {
        if (!class_exists('Flagship_WC_Shipping_Method')) {
            include_once FLS__PLUGIN_DIR.'includes/class.flagship-wc-shipping-method.php';
        }
    }

    public static function add_meta_boxes_action()
    {
        add_meta_box(
            'wc-flagship-shipping-box',
            __('Flagship', 'flagship-shipping'),
            array('WC_Meta_Box_Order_Flagship_Shipping_Actions', 'output'),
            'shop_order',
            'side',
            'high'
        );
    }

    public static function woocommerce_process_shop_order_meta_action($post_id, $post)
    {
        WC_Meta_Box_Order_Flagship_Shipping_Actions::save($post_id, $post);
    }
}

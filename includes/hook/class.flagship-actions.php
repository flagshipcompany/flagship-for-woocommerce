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
            array(__CLASS__, 'order_shipping_meta_box'),
            'shop_order',
            'side',
            'high'
        );

        foreach (wc_get_order_types('order-meta-boxes') as $type) {
            add_meta_box('woocommerce-order-actions', sprintf(__('%s asjdoasd  Actions', 'woocommerce'), $order_type_object->labels->singular_name), 'WC_Meta_Box_Order_Actions::output', $type, 'side', 'high');
        }
    }

    public static function order_shipping_meta_box($post)
    {
        WC_Meta_Box_Flagship_Shipping_Actions::output($post);
    }

    public static function woocommerce_add_order_item_meta_action($item_id, $values, $cart_item_key)
    {
        console('woocommerce_add_order_item_meta_action');
        console($item_id);
        console($values);
        console($cart_item_key);
    }
}

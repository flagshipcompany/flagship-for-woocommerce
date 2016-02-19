<?php

require_once FLS__PLUGIN_DIR.'includes/admin/meta-boxes/class.wc-meta-box-order-flagship-shipping-actions.php';
require_once FLS__PLUGIN_DIR.'includes/admin/post-types/class.wc-admin-post-types-flagship-shipping-pickup.php';

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
        WC_Meta_Box_Order_Flagship_Shipping_Actions::register();
    }

    public static function woocomerce_register_post_type_action()
    {
        Wc_Admin_Post_Types_Flagship_Shipping_Pickup::register();
    }

    public static function woocommerce_process_shop_order_meta_action($post_id, $post)
    {
        // WC_Meta_Box_Order_Flagship_Shipping_Actions::save($post_id, $post);
    }

    public static function pickup_bulk_admin_footer_action()
    {
        // Wc_Admin_Post_Types_Flagship_Shipping_Pickup_Actions::init();
    }

    public static function pickup_bulk_action_save_action()
    {
        // Wc_Admin_Post_Types_Flagship_Shipping_Pickup_Actions::save();
    }
}

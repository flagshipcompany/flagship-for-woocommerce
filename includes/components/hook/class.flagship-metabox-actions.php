<?php

require_once __DIR__.'/class.flagship-api-hooks.php';
require_once FLAGSHIP_SHIPPING_PLUGIN_DIR.'includes/admin/meta-boxes/class.wc-meta-box-order-flagship-shipping-actions.php';
require_once FLAGSHIP_SHIPPING_PLUGIN_DIR.'includes/admin/post-types/class.wc-admin-post-types-flagship-shipping-pickup.php';

class Flagship_Metabox_Actions extends Flagship_Api_Hooks
{
    protected $type = 'action';

    public function bootstrap()
    {
        if (!is_admin()) {
            return;
        }

        // add meta boxes (eg: side box)
        $this->add('add_meta_boxes');
        $this->add('woocommerce_process_shop_order_meta');

        // add pickup custom post type
        $this->add('init', 'woocomerce_register_post_type_action');
    }

    public function add_meta_boxes_action()
    {
        WC_Meta_Box_Order_Flagship_Shipping_Actions::register();
    }

    public function woocomerce_register_post_type_action()
    {
        Wc_Admin_Post_Types_Flagship_Shipping_Pickup::register();
    }

    public function woocommerce_process_shop_order_meta_action($post_id, $post)
    {
        WC_Meta_Box_Order_Flagship_Shipping_Actions::save($post_id, $post);
    }
}

<?php

require_once FLS__PLUGIN_DIR.'includes/admin/meta-boxes/class.wc-meta-box-order-flagship-shipping-actions.php';
require_once FLS__PLUGIN_DIR.'includes/admin/post-types/class.wc-admin-post-types-flagship-shipping-pickup-actions.php';

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

    public static function woocomerce_register_post_type_action()
    {
        register_post_type( 'shop_pickup',
            apply_filters( 'woocommerce_register_post_type_shop_pickup',
                array(
                    'labels'              => array(
                            'name'               => __( 'Pick-ups', 'woocommerce' ),
                            'singular_name'      => __( 'Pick-up', 'woocommerce' ),
                            'menu_name'          => _x( 'Pick-ups', 'Admin menu name', 'woocommerce' ),
                            'add_new'            => __( 'Add Pick-up', 'woocommerce' ),
                            'add_new_item'       => __( 'Schedule New Pick-up', 'woocommerce' ),
                            'edit'               => __( 'Edit', 'woocommerce' ),
                            'edit_item'          => __( 'Edit Coupon', 'woocommerce' ),
                            'new_item'           => __( 'New Coupon', 'woocommerce' ),
                            'view'               => __( 'View Pick-ups', 'woocommerce' ),
                            'view_item'          => __( 'View Pick-up', 'woocommerce' ),
                            'search_items'       => __( 'Search Pick-ups', 'woocommerce' ),
                            'not_found'          => __( 'No Coupons found', 'woocommerce' ),
                            'not_found_in_trash' => __( 'No Coupons found in trash', 'woocommerce' ),
                            'parent'             => __( 'Parent Coupon', 'woocommerce' )
                        ),
                    'description'         => __( 'This is where you can add new coupons that customers can use in your store.', 'woocommerce' ),
                    'public'              => false,
                    'show_ui'             => true,
                    'capability_type'     => 'shop_coupon',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => false,
                    'exclude_from_search' => true,
                    'show_in_menu'        => current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
                    'hierarchical'        => false,
                    'rewrite'             => false,
                    'query_var'           => false,
                    'supports'            => array( 'title' ),
                    'show_in_nav_menus'   => false,
                    'show_in_admin_bar'   => true
                )
            )
        );
    }

    public static function woocommerce_process_shop_order_meta_action($post_id, $post)
    {
        WC_Meta_Box_Order_Flagship_Shipping_Actions::save($post_id, $post);
    }

    public static function pickup_bulk_admin_footer_action()
    {
        Wc_Admin_Post_Types_Flagship_Shipping_Pickup_Actions::init();
    }

    public static function pickup_bulk_action_save_action()
    {
        Wc_Admin_Post_Types_Flagship_Shipping_Pickup_Actions::save();
    }
}

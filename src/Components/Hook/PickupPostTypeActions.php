<?php

namespace FS\Components\Hook;

class PickupPostTypeActions extends Engine implements Factory\HookRegisterAwareInterface
{
    protected $type = 'action';

    public function register()
    {
        \register_post_type(
            'flagship_pickup',
            \apply_filters(
                'woocommerce_register_post_type_flagship_shipping_pickup',
                array(
                    'labels' => array(
                        'name' => __('Pick-ups', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'singular_name' => __('Pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'menu_name' => _x('Pick-ups', 'Admin menu name', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'add_new' => __('Add Pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'add_new_item' => __('Schedule New Pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'edit' => __('Edit', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'edit_item' => __('Edit Coupon', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'new_item' => __('New Coupon', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'view' => __('View Pick-ups', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'view_item' => __('View Pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'search_items' => __('Search Pick-ups', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'not_found' => __('No Pick-ups found', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'not_found_in_trash' => __('No Pick-ups found in trash', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                        'parent' => __('Parent pick-ups', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    ),
                    'description' => __('This is where you can add new coupons that customers can use in your store.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'public' => false,
                    'show_ui' => true,
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'show_in_menu' => 'woocommerce',
                    'hierarchical' => false,
                    'rewrite' => false,
                    'query_var' => false,
                    'supports' => array('title'),
                    'show_in_nav_menus' => false,
                    'show_in_admin_bar' => true,
                    'capabilities' => array(
                        'create_posts' => false,
                    ),
                    'map_meta_cap' => true,
                )
            )
        );

        if (!is_admin()) {
            return;
        }

        // add_filter('manage_edit-flagship_pickup_columns', array($this, 'flagship_pickup_columns'), 10, 1);

        // add_filter('bulk_actions-edit-flagship_pickup', array($this, 'flagship_pickup_bulk_actions'), 10, 1);
        add_filter('bulk_actions-edit-shop_order', array($this, 'shop_order_flagship_pickup_schedule_bulk_actions'), 10, 1);

        add_filter('list_table_primary_column', array($this, 'list_table_primary_column'), 10, 2);

        add_action('load-edit.php', array($this, 'save'));

        // add_action('admin_menu', function(){
        //     add_submenu_page('woocommerce', __('Pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN), _x('Pick-ups', 'Admin menu name', FLAGSHIP_SHIPPING_TEXT_DOMAIN), 'create_posts', 'edit.php?post_type=flagship_pickup');
        // }, 12);
    }

    public function list_table_primary_column($default, $screen_id)
    {
        if ('edit-flagship_pickup' === $screen_id) {
            return 'pickup_date';
        }

        return $default;
    }

    public function save()
    {
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();
        $postIds = array_map('absint', (array) $_REQUEST['post']);

        switch ($action) {
            case 'flagship_shipping_pickup_schedule':
                $pickup = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\Pickup');
                $pickup->schedulePickup($pickup->makeShoppingOrders($postIds));
                break;
            case 'flagship_shipping_pickup_void':
                $pickup = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\Pickup');
                $pickup->voidPickup($postIds);
                break;
            case 'flagship_shipping_pickup_reschedule':
                $pickup = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\Pickup');
                $pickup->reschedulePickup($postIds);
                break;
        }
    }
}

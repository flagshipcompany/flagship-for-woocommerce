<?php

class Wc_Admin_Post_Types_Flagship_Shipping_Pickup
{
    public static function register()
    {
        // post type name cannot exceed 20 characters !!!
        //
        register_post_type(
            'flagship_pickup',
            apply_filters('woocommerce_register_post_type_flagship_shipping_pickup',
                array(
                    'labels' => array(
                            'name' => __('Pick-ups', 'woocommerce'),
                            'singular_name' => __('Pick-up', 'woocommerce'),
                            'menu_name' => _x('Pick-ups', 'Admin menu name', 'woocommerce'),
                            'add_new' => __('Add Pick-up', 'woocommerce'),
                            'add_new_item' => __('Schedule New Pick-up', 'woocommerce'),
                            'edit' => __('Edit', 'woocommerce'),
                            'edit_item' => __('Edit Coupon', 'woocommerce'),
                            'new_item' => __('New Coupon', 'woocommerce'),
                            'view' => __('View Pick-ups', 'woocommerce'),
                            'view_item' => __('View Pick-up', 'woocommerce'),
                            'search_items' => __('Search Pick-ups', 'woocommerce'),
                            'not_found' => __('No Pick-ups found', 'woocommerce'),
                            'not_found_in_trash' => __('No Pick-ups found in trash', 'woocommerce'),
                            'parent' => __('Parent pick-ups', 'woocommerce'),
                        ),
                    'description' => __('This is where you can add new coupons that customers can use in your store.', 'woocommerce'),
                    'public' => false,
                    'show_ui' => true,
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'show_in_menu' => current_user_can('manage_woocommerce') ? 'woocommerce' : true,
                    'hierarchical' => false,
                    'rewrite' => false,
                    'query_var' => false,
                    'supports' => array('title'),
                    'show_in_nav_menus' => false,
                    'show_in_admin_bar' => true,
                )
            )
        );

        if (!is_admin()) {
            return;
        }

        add_filter('manage_edit-flagship_pickup_columns', array(__CLASS__, 'flagship_pickup_columns'), 10, 1);

        add_action('manage_flagship_pickup_posts_custom_column', array(__CLASS__, 'render_flagship_pickup_columns'), 10, 2);

        add_filter('bulk_actions-edit-flagship_pickup', array(__CLASS__, 'flagship_pickup_bulk_actions'), 10, 1);
        add_filter('bulk_actions-edit-shop_order', array(__CLASS__, 'shop_order_flagship_pickup_schedule_bulk_actions'), 10, 1);

        add_filter('list_table_primary_column', array(__CLASS__, 'list_table_primary_column'), 10, 2);

        add_action('load-edit.php', array(__CLASS__, 'save'));
    }

    public static function render_flagship_pickup_columns($column, $post_id)
    {
        global $post, $woocommerce;

        switch ($column) {
            case 'order_ids':
                $order_ids = get_post_meta($post_id, 'order_ids', true);
                $id = array_pop($order_ids);
                $output = '<a href="'.admin_url('post.php?post='.$id.'&action=edit').'"><strong>#'.$id.'</strong></a>';

                foreach ($order_ids as $order_id) {
                    $output .= ', <a href="'.admin_url('post.php?post='.absint($order_id).'&action=edit').'"><strong>#'.$order_id.'</strong></a>';
                }

                echo $output;

                break;
            case 'confirmation':
                $confirmation = get_post_meta($post_id, 'confirmation', true);
                echo $confirmation;
                break;
            case 'boxes':
                $boxes = get_post_meta($post_id, 'boxes', true);
                echo $boxes;
                break;
            case 'weight':
                $weight = get_post_meta($post_id, 'weight', true);
                echo $weight;
                break;
            case 'courier':
                $courier = get_post_meta($post_id, 'courier', true);
                echo $courier;
                break;
            case 'courier':
                $pickup_date = get_post_meta($post_id, 'pickup_date', true);
                echo $pickup_date;
                break;
            case 'shipping_address':
                $address = Flagship_Request_Formatter::get_address_from();
                echo $address['city'].', '.$address['state'].' '.$address['postal_code'];
                break;
            case 'pickup_date':
                $date = get_post_meta($post_id, 'pickup_date', true);
                echo '<abbr>'.$date.'</abbr>';
                break;
            case 'pickup_action':
                $confirmation = get_post_meta($post_id, 'confirmation', true);

                if ($confirmation) {
                    echo '<a href="" class="button">Void</a>';
                } else {
                    echo '<a href="" class="button">Reschedule</a>';
                }
                break;
            default:
                break;
        }
    }

    public static function save_pickup($pickup = array())
    {
        $pickup_id = wp_insert_post(array(
            'post_title' => 'pickup title',
            'post_content' => '',
            'post_type' => 'flagship_pickup',
            'post_status' => 'publish',
            'meta_input' => $pickup,
        ));

        return $pickup_id;
    }

    public static function flagship_pickup_columns($columns)
    {
        $columns = array();
        $columns['cb'] = '<input type="checkbox"/>';
        $columns['order_ids'] = __('Order IDs', 'flagship-shipping');

        $columns['confirmation'] = '<span class="status_head tips" data-tip="'.esc_attr__('Confirmation', 'flagship-shipping').'">'.esc_attr__('Confirmation', 'flagship-shipping').'</span>';
        $columns['courier'] = __('Courier', 'flagship-shipping');
        $columns['boxes'] = __('#. Boxes', 'flagship-shipping');
        $columns['weight'] = __('Weight', 'flagship-shipping');
        $columns['shipping_address'] = __('Address', 'flagship-shipping');
        $columns['time_interval'] = __('Pick-up Time', 'flagship-shipping');
        $columns['pickup_date'] = __('Pick-up Date', 'flagship-shipping');
        $columns['pickup_action'] = __('Actions', 'flagship-shipping');

        return $columns;
    }

    public function flagship_pickup_sortable_columns($columns)
    {
        $custom = array(
            'order_title' => 'ID',
            'order_total' => 'order_total',
            'order_date' => 'date',
        );
        unset($columns['comments']);

        return wp_parse_args($custom, $columns);
    }

    public static function flagship_pickup_bulk_actions($actions)
    {
        if (isset($actions['edit'])) {
            unset($actions['edit']);
        }

        global $post_type;

        if ($post_type == 'flagship_pickup') {
            ?>
            <script type="text/javascript">
            jQuery(function() {
                jQuery('<option>').val('flagship_shipping_pickup_void').text('<?php _e('Void pick-ups', 'flagship-shipping')?>').appendTo('select[name="action"]');
                jQuery('<option>').val('flagship_shipping_pickup_void').text('<?php _e('Void pick-ups', 'flagship-shipping')?>').appendTo('select[name="action2"]');
            });
            </script>
            <?php

        }

        return $actions;
    }

    public function list_table_primary_column($default, $screen_id)
    {
        if ('edit-flagship_pickup' === $screen_id) {
            return 'pickup_date';
        }

        return $default;
    }

    public static function init()
    {
        return new self();
    }

    public static function shop_order_flagship_pickup_schedule_bulk_actions($actions)
    {
        global $post_type;

        if ($post_type == 'shop_order') {
            ?>
            <script type="text/javascript">
            jQuery(function() {
                jQuery('<option>').val('flagship_shipping_pickup_schedule').text('<?php _e('Schedule pick-up', 'woocommerce')?>').appendTo('select[name="action"]');
                jQuery('<option>').val('flagship_shipping_pickup_schedule').text('<?php _e('Schedule pick-up', 'woocommerce')?>').appendTo('select[name="action2"]');
            });
            </script>
            <?php

        }

        return $actions;
    }

    public static function save()
    {
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();

        // Bail out if this is not a status-changing action
        if ($action != 'flagship_shipping_pickup_schedule') {
            return;
        }

        $post_ids = array_map('absint', (array) $_REQUEST['post']);
        $courier_shippings = self::get_shippings_per_courier($post_ids);

        $requests = Flagship_Request_Formatter::get_multiple_pickup_schedule_request($courier_shippings);
        $flagship = Flagship_Application::get_instance();

        foreach ($requests as $request) {
            $order_ids = $request['order_ids'];

            unset($request['order_ids']);

            $response = $flagship->client()->post(
                '/pickups',
                $request
            );

            if ($response->is_success()) {
                $pickup = $response->get_content()['content'];

                $pickup['order_ids'] = $order_ids;
                $pickup['pickup_date'] = $pickup['date'];

                $pickup_id = self::save_pickup($pickup);
            }
        }

        $sendback = add_query_arg(array('post_type' => 'flagship_pickup', 'ids' => implode(',', $post_ids)), '');

        wp_redirect(esc_url_raw($sendback));
        exit();
    }

    protected static function get_shippings_per_courier($post_ids)
    {
        $data = array(
            'fedex' => array(),
            'purolator' => array(),
            'ups' => array(),
        );

        foreach ($post_ids as $post_id) {
            $order = wc_get_order($post_id);
            $shipment = get_post_meta($order->id, 'flagship_shipping_raw', true);

            if (empty($shipment)) {
                continue;
            }

            $data[strtolower($shipment['service']['courier_name'])][$order->id] = array(
                'shipment' => $shipment,
                'order' => $order,
                'date' => date('Y-m-d'),
            );
        }

        return $data;
    }
}

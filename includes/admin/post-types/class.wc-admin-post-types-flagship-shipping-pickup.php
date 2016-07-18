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
                    'show_in_menu' => current_user_can('manage_woocommerce') ? 'woocommerce' : true,
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

        $flagship = Flagship_Application::get_instance();

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
                $id = get_post_meta($post_id, 'id', true);
                $confirmation = get_post_meta($post_id, 'confirmation', true);
                echo $confirmation.'<br/><small class="meta">'.$id.'</small>';
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
                $address = $flagship['address']->get_from();
                echo $address['postal_code'].'<br/><small class="meta">'.$address['city'].', '.$address['state'].'</small>';
                break;
            case 'time_interval':
                $date = get_post_meta($post_id, 'pickup_date', true);
                $from = date('H:i', strtotime(get_post_meta($post_id, 'from', true)));
                $until = date('H:i', strtotime(get_post_meta($post_id, 'until', true)));
                echo '<abbr title="'.date('l', strtotime($date)).'">'.$date.'</abbr><br/><small class="meta">'.$from.'-'.$until.'</small>';
                break;
            case 'pickup_status':
                $status = get_post_meta($post_id, 'cancelled', true);
                echo $status ? '<span style="color:#d54e21;">cancelled</span>' : 'scheduled';
                break;
            case 'pickup_action':
                $cancelled = get_post_meta($post_id, 'cancelled', true);

                if (!$cancelled) {
                    ?>
                <button class="button pickup-void" data-checkbox-id="<?php echo 'cb-select-'.$post_id;
                    ?>">Void</button>
                <?php

                } else {
                    ?>
                <button class="button button-primary pickup-reschedule" data-checkbox-id="<?php echo 'cb-select-'.$post_id;
                    ?>">Reschedule</button>
                <?php

                }
                break;
            default:
                break;
        }
    }

    public static function flagship_pickup_columns($columns)
    {
        $columns = array();
        $columns['cb'] = '<input type="checkbox"/>';
        $columns['order_ids'] = __('Order IDs', FLAGSHIP_SHIPPING_TEXT_DOMAIN);

        $columns['confirmation'] = '<span class="status_head tips" data-tip="'.esc_attr__('Confirmation / ID', FLAGSHIP_SHIPPING_TEXT_DOMAIN).'">'.esc_attr__('Confirmation / ID', FLAGSHIP_SHIPPING_TEXT_DOMAIN).'</span>';
        $columns['courier'] = __('Courier', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $columns['boxes'] = __('#. Boxes', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $columns['weight'] = __('Weight', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $columns['shipping_address'] = __('Address', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $columns['time_interval'] = __('Pick-up Time', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $columns['pickup_status'] = __('Status', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $columns['pickup_action'] = __('Actions', FLAGSHIP_SHIPPING_TEXT_DOMAIN);

        return $columns;
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
            (function($){
                $(function(){
                    $('<option>').prop('disabled', true).text('-----------').prependTo('select[name="action"]');
                    $('<option>').val('flagship_shipping_pickup_void').text('<?php _e('Void pick-ups', FLAGSHIP_SHIPPING_TEXT_DOMAIN)?>').prependTo('select[name="action"]');
                    $('<option>').prop('disabled', true).text('-----------').prependTo('select[name="action2"]');
                    $('<option>').val('flagship_shipping_pickup_void').text('<?php _e('Void pick-ups', FLAGSHIP_SHIPPING_TEXT_DOMAIN)?>').prependTo('select[name="action2"]');
                    $('<option>').val('flagship_shipping_pickup_reschedule').text('<?php _e('Reschedule pick-ups', FLAGSHIP_SHIPPING_TEXT_DOMAIN)?>').prependTo('select[name="action"]');
                    $('<option>').val('flagship_shipping_pickup_reschedule').text('<?php _e('Reschedule pick-ups', FLAGSHIP_SHIPPING_TEXT_DOMAIN)?>').prependTo('select[name="action2"]');
                    $('.pickup-void').click(function(e){
                        e.preventDefault();
                        $('#'+$(this).attr('data-checkbox-id')).prop('checked', true);
                        $('select[name="action"]').val('flagship_shipping_pickup_void');
                        $('#doaction').trigger('click');
                    });

                    $('.pickup-reschedule').click(function(e){
                        e.preventDefault();
                        $('#'+$(this).attr('data-checkbox-id')).prop('checked', true);
                        $('select[name="action"]').val('flagship_shipping_pickup_reschedule');
                        $('#doaction').trigger('click');
                    });
                });
            })(jQuery);
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
            (function($){
                $(function(){
                    $('<option>').prop('disabled', true).text('-----------').prependTo('select[name="action"]');
                    $('<option>').val('flagship_shipping_pickup_schedule').text('<?php _e('Schedule pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN)?>').prependTo('select[name="action"]');
                    $('<option>').val('flagship_shipping_pickup_schedule').text('<?php _e('Schedule pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN)?>').prependTo('select[name="action2"]');
                });
            })(jQuery);
            </script>
            <?php

        }

        return $actions;
    }

    public static function save()
    {
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();
        $post_ids = array_map('absint', (array) $_REQUEST['post']);

        if ($action == 'flagship_shipping_pickup_schedule') {
            self::schedule_pickup($post_ids);
        } elseif ($action == 'flagship_shipping_pickup_void') {
            self::void_pickup($post_ids);
        } elseif ($action == 'flagship_shipping_pickup_reschedule') {
            self::reschedule_pickup($post_ids);
        }
    }

    protected static function schedule_pickup($post_ids)
    {
        $flagship = Flagship_Application::get_instance();

        $flagship->load('Pickup');

        $courier_shippings = self::get_shippings_per_courier($post_ids);

        $requests = $flagship['pickup']->get_multiple_pickup_schedule_request($courier_shippings);

        foreach ($requests as $request) {
            $order_ids = $request['order_ids'];

            unset($request['order_ids']);

            $response = $flagship['client']->post(
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
    }

    protected static function void_pickup($post_ids)
    {
        $flagship = Flagship_Application::get_instance();

        foreach ($post_ids as $post_id) {
            if ($pickup_id = get_post_meta($post_id, 'id', true)) {
                $response = $flagship['client']->delete('/pickups/'.$pickup_id);

                if ($response->is_success() || $response->get_code() == 409) {
                    update_post_meta($post_id, 'cancelled', true);
                }
            }
        }

        $sendback = add_query_arg(array('post_type' => 'flagship_pickup'), '');
        wp_redirect(esc_url_raw($sendback));
    }

    protected static function reschedule_pickup($post_ids)
    {
        $flagship = Flagship_Application::get_instance();

        $flagship->load('Pickup');

        foreach ($post_ids as $post_id) {
            if ($pickup_id = get_post_meta($post_id, 'id', true)
                && $cancelled = get_post_meta($post_id, 'cancelled', true)
            ) {
                $order_ids = get_post_meta($post_id, 'order_ids', true);

                $courier_shippings = self::get_shippings_per_courier($order_ids);
                $requests = $flagship['pickup']->get_multiple_pickup_schedule_request($courier_shippings);

                foreach ($requests as $request) {
                    $order_ids = $request['order_ids'];

                    unset($request['order_ids']);

                    $response = $flagship['client']->post(
                        '/pickups',
                        $request
                    );

                    if ($response->is_success()) {
                        $pickup = $response->get_content()['content'];

                        $pickup['order_ids'] = $order_ids;
                        $pickup['pickup_date'] = $pickup['date'];

                        $pickup_id = self::save_pickup($pickup, $post_id);
                    }
                }
            }
        }

        $sendback = add_query_arg(array('post_type' => 'flagship_pickup', 'ids' => implode(',', $post_ids)), '');
        wp_redirect(esc_url_raw($sendback));
    }

    protected static function save_pickup($pickup = array(), $id = null)
    {
        if (!$id) {
            $pickup_id = wp_insert_post(array(
                'post_title' => 'pickup title',
                'post_content' => '',
                'post_type' => 'flagship_pickup',
                'post_status' => 'publish',
                'meta_input' => $pickup,
            ));

            return $pickup_id;
        }

        foreach ($pickup as $key => $value) {
            update_post_meta($id, $key, $value);
        }

        return $id;
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

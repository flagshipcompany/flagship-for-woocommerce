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

        add_filter('manage_edit-flagship_pickup_columns', array($this, 'flagship_pickup_columns'), 10, 1);

        add_action('manage_flagship_pickup_posts_custom_column', array($this, 'render_flagship_pickup_columns'), 10, 2);

        add_filter('bulk_actions-edit-flagship_pickup', array($this, 'flagship_pickup_bulk_actions'), 10, 1);
        add_filter('bulk_actions-edit-shop_order', array($this, 'shop_order_flagship_pickup_schedule_bulk_actions'), 10, 1);

        add_filter('list_table_primary_column', array($this, 'list_table_primary_column'), 10, 2);

        add_action('load-edit.php', array($this, 'save'));
    }

    public function render_flagship_pickup_columns($column, $post_id)
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
                $shipperAddressBuilder = $this->getApplicationContext()
                    ->getComponent('\\FS\\Components\\Shipping\\RequestBuilder\\ShipperAddressBuilder');
                $options = $this->getApplicationContext()
                    ->getComponent('\\FS\\Components\\Options');

                $address = $shipperAddressBuilder->build($options);

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

    public function flagship_pickup_columns($columns)
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

    public function flagship_pickup_bulk_actions($actions)
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

    public function shop_order_flagship_pickup_schedule_bulk_actions($actions)
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

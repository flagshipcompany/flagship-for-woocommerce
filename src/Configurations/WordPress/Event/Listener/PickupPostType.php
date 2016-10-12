<?php

namespace FS\Configurations\WordPress\Event\Listener;

class PickupPostType extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface, \FS\Configurations\WordPress\Event\NativeHookInterface
{
    public function getSupportedEvent()
    {
        return false;
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
        $type = $event->getInput('type');
        $postIds = $event->getInputs('postIds');
        $pickup = $context->getComponent('\\FS\\Components\\Order\\Pickup');

        switch ($type) {
            case 'schedule':
                $pickup->schedulePickup($pickup->makeShoppingOrders($postIds));
                break;
            case 'void':
                $pickup->voidPickup($postIds);
                break;
            case 'reschedule':
                $pickup->reschedulePickup($postIds);
                break;
        }
    }

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context)
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

        \add_filter('manage_edit-flagship_pickup_columns', function ($columns) {
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
        }, 10, 1);

        \add_action('manage_flagship_pickup_posts_custom_column', function ($column, $post_id) use ($context) {

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
                    $factory = $context
                        ->getComponent('\\FS\\Components\\Shipping\\RequestBuilder\\Factory\\RequestBuilderFactory');
                    $options = $context
                        ->getComponent('\\FS\\Components\\Options');

                    $builder = $factory->getBuilder('ShipperAddress');

                    $address = $builder->build(array(
                        'options' => $options,
                    ));

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
        }, 10, 2);

        \add_filter('bulk_actions-edit-flagship_pickup', function ($actions) {
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
        }, 10, 1);

        \add_filter('bulk_actions-edit-shop_order', function ($actions) {
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
        }, 10, 1);

        \add_filter('list_table_primary_column', function ($default, $screen_id) {
            if ('edit-flagship_pickup' === $screen_id) {
                return 'pickup_date';
            }

            return $default;
        }, 10, 2);

        \add_action('load-edit.php', function () use ($context) {
            $wp_list_table = _get_list_table('WP_Posts_List_Table');
            $action = $wp_list_table->current_action();
            $postIds = array_map('absint', (array) $_REQUEST['post']);

            $event = new \FS\Configurations\WordPress\Event\PickupPostTypeEvent();

            switch ($action) {
                case 'flagship_shipping_pickup_schedule':
                    $event->setInputs(array(
                        'type' => 'schedule',
                        'postIds' => $postIds,
                    ));

                    $context->publishEvent($event);
                    break;
                case 'flagship_shipping_pickup_void':
                    $event->setInputs(array(
                        'type' => 'void',
                        'postIds' => $postIds,
                    ));

                    $context->publishEvent($event);
                    break;
                case 'flagship_shipping_pickup_reschedule':
                    $event->setInputs(array(
                        'type' => 'reschedule',
                        'postIds' => $postIds,
                    ));

                    $context->publishEvent($event);
                    break;
            }
        });
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}

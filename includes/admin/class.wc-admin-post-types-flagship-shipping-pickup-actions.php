<?php

class Wc_Admin_Post_Types_Flagship_Shipping_Pickup_Actions
{
    protected $flagship;

    public function __construct()
    {
        $this->flagship = Flagship_Application::get_instance();
        
        add_filter('manage_shop_pickup_posts_columns', array($this, 'shop_order_columns'));
        add_action('manage_shop_pickup_posts_custom_column', array($this, 'render_shop_order_columns'), 2);
        add_filter('manage_edit-shop_pickup_sortable_columns', array($this, 'shop_order_sortable_columns'));
        add_filter('bulk_actions-edit-shop_order', array($this, 'shop_order_bulk_actions'));
        add_filter('list_table_primary_column', array($this, 'list_table_primary_column'), 10, 2);
    }

    /**
     * Define custom columns for orders.
     *
     * @param array $existing_columns
     *
     * @return array
     */
    public function shop_order_columns($existing_columns)
    {
        $columns = array();
        $columns['cb'] = $existing_columns['cb'];
        $columns['order_status'] = '<span class="status_head tips" data-tip="'.esc_attr__('Status', 'woocommerce').'">'.esc_attr__('Status', 'woocommerce').'</span>';
        $columns['order_title'] = __('Order', 'woocommerce');
        $columns['order_items'] = __('Purchased', 'woocommerce');
        $columns['billing_address'] = __('Billing', 'woocommerce');
        $columns['shipping_address'] = __('Ship to', 'woocommerce');
        $columns['customer_message'] = '<span class="notes_head tips" data-tip="'.esc_attr__('Customer Message', 'woocommerce').'">'.esc_attr__('Customer Message', 'woocommerce').'</span>';
        $columns['order_notes'] = '<span class="order-notes_head tips" data-tip="'.esc_attr__('Order Notes', 'woocommerce').'">'.esc_attr__('Order Notes', 'woocommerce').'</span>';
        $columns['order_date'] = __('Date', 'woocommerce');
        $columns['order_total'] = __('Total', 'woocommerce');
        $columns['order_actions'] = __('Actions', 'woocommerce');

        return $columns;
    }

    /**
     * Output custom columns for coupons.
     *
     * @param string $column
     */
    public function render_shop_order_columns($column)
    {
        global $post, $woocommerce, $the_order;

        if (empty($the_order) || $the_order->id != $post->ID) {
            $the_order = wc_get_order($post->ID);
        }

        switch ($column) {
            case 'order_status' :

                printf('<mark class="%s tips" data-tip="%s">%s</mark>', sanitize_title($the_order->get_status()), wc_get_order_status_name($the_order->get_status()), wc_get_order_status_name($the_order->get_status()));

            break;
            case 'order_date' :

                if ('0000-00-00 00:00:00' == $post->post_date) {
                    $t_time = $h_time = __('Unpublished', 'woocommerce');
                } else {
                    $t_time = get_the_time(__('Y/m/d g:i:s A', 'woocommerce'), $post);
                    $h_time = get_the_time(__('Y/m/d', 'woocommerce'), $post);
                }

                echo '<abbr title="'.esc_attr($t_time).'">'.esc_html(apply_filters('post_date_column_time', $h_time, $post)).'</abbr>';

            break;
            case 'customer_message' :
                if ($the_order->customer_message) {
                    echo '<span class="note-on tips" data-tip="'.wc_sanitize_tooltip($the_order->customer_message).'">'.__('Yes', 'woocommerce').'</span>';
                } else {
                    echo '<span class="na">&ndash;</span>';
                }

            break;
            case 'order_items' :

                echo '<a href="#" class="show_order_items">'.apply_filters('woocommerce_admin_order_item_count', sprintf(_n('%d item', '%d items', $the_order->get_item_count(), 'woocommerce'), $the_order->get_item_count()), $the_order).'</a>';

                if (sizeof($the_order->get_items()) > 0) {
                    echo '<table class="order_items" cellspacing="0">';

                    foreach ($the_order->get_items() as $item) {
                        $product = apply_filters('woocommerce_order_item_product', $the_order->get_product_from_item($item), $item);
                        $item_meta = new WC_Order_Item_Meta($item, $product);
                        $item_meta_html = $item_meta->display(true, true);
                        ?>
                        <tr class="<?php echo apply_filters('woocommerce_admin_order_item_class', '', $item);
                        ?>">
                            <td class="qty"><?php echo absint($item['qty']);
                        ?></td>
                            <td class="name">
                                <?php  if ($product) : ?>
                                    <?php echo(wc_product_sku_enabled() && $product->get_sku()) ? $product->get_sku().' - ' : '';
                        ?><a href="<?php echo get_edit_post_link($product->id);
                        ?>" title="<?php echo apply_filters('woocommerce_order_item_name', $item['name'], $item, false);
                        ?>"><?php echo apply_filters('woocommerce_order_item_name', $item['name'], $item, false);
                        ?></a>
                                <?php else : ?>
                                    <?php echo apply_filters('woocommerce_order_item_name', $item['name'], $item, false);
                        ?>
                                <?php endif;
                        ?>
                                <?php if (!empty($item_meta_html)) : ?>
                                    <?php echo wc_help_tip($item_meta_html);
                        ?>
                                <?php endif;
                        ?>
                            </td>
                        </tr>
                        <?php

                    }

                    echo '</table>';
                } else {
                    echo '&ndash;';
                }
            break;
            case 'billing_address' :

                if ($address = $the_order->get_formatted_billing_address()) {
                    echo esc_html(preg_replace('#<br\s*/?>#i', ', ', $address));
                } else {
                    echo '&ndash;';
                }

                if ($the_order->billing_phone) {
                    echo '<small class="meta">'.__('Tel:', 'woocommerce').' '.esc_html($the_order->billing_phone).'</small>';
                }

            break;
            case 'shipping_address' :

                if ($address = $the_order->get_formatted_shipping_address()) {
                    echo '<a target="_blank" href="'.esc_url($the_order->get_shipping_address_map_url()).'">'.esc_html(preg_replace('#<br\s*/?>#i', ', ', $address)).'</a>';
                } else {
                    echo '&ndash;';
                }

                if ($the_order->get_shipping_method()) {
                    echo '<small class="meta">'.__('Via', 'woocommerce').' '.esc_html($the_order->get_shipping_method()).'</small>';
                }

            break;
            case 'order_notes' :

                if ($post->comment_count) {

                    // check the status of the post
                    $status = ('trash' !== $post->post_status) ? '' : 'post-trashed';

                    $latest_notes = get_comments(array(
                        'post_id' => $post->ID,
                        'number' => 1,
                        'status' => $status,
                    ));

                    $latest_note = current($latest_notes);

                    if ($post->comment_count == 1) {
                        echo '<span class="note-on tips" data-tip="'.wc_sanitize_tooltip($latest_note->comment_content).'">'.__('Yes', 'woocommerce').'</span>';
                    } elseif (isset($latest_note->comment_content)) {
                        echo '<span class="note-on tips" data-tip="'.wc_sanitize_tooltip($latest_note->comment_content.'<br/><small style="display:block">'.sprintf(_n('plus %d other note', 'plus %d other notes', ($post->comment_count - 1), 'woocommerce'), $post->comment_count - 1).'</small>').'">'.__('Yes', 'woocommerce').'</span>';
                    } else {
                        echo '<span class="note-on tips" data-tip="'.wc_sanitize_tooltip(sprintf(_n('%d note', '%d notes', $post->comment_count, 'woocommerce'), $post->comment_count)).'">'.__('Yes', 'woocommerce').'</span>';
                    }
                } else {
                    echo '<span class="na">&ndash;</span>';
                }

            break;
            case 'order_total' :
                echo $the_order->get_formatted_order_total();

                if ($the_order->payment_method_title) {
                    echo '<small class="meta">'.__('Via', 'woocommerce').' '.esc_html($the_order->payment_method_title).'</small>';
                }
            break;
            case 'order_title' :

                if ($the_order->user_id) {
                    $user_info = get_userdata($the_order->user_id);
                }

                if (!empty($user_info)) {
                    $username = '<a href="user-edit.php?user_id='.absint($user_info->ID).'">';

                    if ($user_info->first_name || $user_info->last_name) {
                        $username .= esc_html(sprintf(_x('%1$s %2$s', 'full name', 'woocommerce'), ucfirst($user_info->first_name), ucfirst($user_info->last_name)));
                    } else {
                        $username .= esc_html(ucfirst($user_info->display_name));
                    }

                    $username .= '</a>';
                } else {
                    if ($the_order->billing_first_name || $the_order->billing_last_name) {
                        $username = trim(sprintf(_x('%1$s %2$s', 'full name', 'woocommerce'), $the_order->billing_first_name, $the_order->billing_last_name));
                    } else {
                        $username = __('Guest', 'woocommerce');
                    }
                }

                printf(_x('%s by %s', 'Order number by X', 'woocommerce'), '<a href="'.admin_url('post.php?post='.absint($post->ID).'&action=edit').'" class="row-title"><strong>#'.esc_attr($the_order->get_order_number()).'</strong></a>', $username);

                if ($the_order->billing_email) {
                    echo '<small class="meta email"><a href="'.esc_url('mailto:'.$the_order->billing_email).'">'.esc_html($the_order->billing_email).'</a></small>';
                }

                echo '<button type="button" class="toggle-row"><span class="screen-reader-text">'.__('Show more details', 'woocommerce').'</span></button>';

            break;
            case 'order_actions' :

                ?><p>
                    <?php
                        do_action('woocommerce_admin_order_actions_start', $the_order);

                        $actions = array();

                        if ($the_order->has_status(array('pending', 'on-hold'))) {
                            $actions['processing'] = array(
                                'url' => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=processing&order_id='.$post->ID), 'woocommerce-mark-order-status'),
                                'name' => __('Processing', 'woocommerce'),
                                'action' => 'processing',
                            );
                        }

                        if ($the_order->has_status(array('pending', 'on-hold', 'processing'))) {
                            $actions['complete'] = array(
                                'url' => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id='.$post->ID), 'woocommerce-mark-order-status'),
                                'name' => __('Complete', 'woocommerce'),
                                'action' => 'complete',
                            );
                        }

                        $actions['view'] = array(
                            'url' => admin_url('post.php?post='.$post->ID.'&action=edit'),
                            'name' => __('View', 'woocommerce'),
                            'action' => 'view',
                        );

                        $actions = apply_filters('woocommerce_admin_order_actions', $actions, $the_order);

                        foreach ($actions as $action) {
                            printf('<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr($action['action']), esc_url($action['url']), esc_attr($action['name']), esc_attr($action['name']));
                        }

                        do_action('woocommerce_admin_order_actions_end', $the_order);
                    ?>
                </p><?php

            break;
        }
    }

    /**
     * Make columns sortable - https://gist.github.com/906872.
     *
     * @param array $columns
     *
     * @return array
     */
    public function shop_order_sortable_columns($columns)
    {
        $custom = array(
            'order_title' => 'ID',
            'order_total' => 'order_total',
            'order_date' => 'date',
        );
        unset($columns['comments']);

        return wp_parse_args($custom, $columns);
    }

    /**
     * Remove edit from the bulk actions.
     *
     * @param array $actions
     *
     * @return array
     */
    public function shop_order_bulk_actions($actions)
    {
        if (isset($actions['edit'])) {
            unset($actions['edit']);
        }

        return $actions;
    }

    /**
     * Set list table primary column for products and orders.
     * Support for WordPress 4.3.
     *
     * @param string $default
     * @param string $screen_id
     *
     * @return string
     */
    public function list_table_primary_column($default, $screen_id)
    {
        if ('edit-shop_pickup' === $screen_id) {
            return 'order_title';
        }

        return $default;
    }

    /**
     * Set row actions for products and orders.
     *
     * @param array   $actions
     * @param WP_Post $post
     *
     * @return array
     */
    public function row_actions($actions, $post)
    {
        if ('shop_pickup' === $post->post_type) {
            return array();
        }

        return $actions;
    }

    public static function init()
    {
        return new self();
    }

    public static function output()
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
    }

    public static function save()
    {
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();

        // Bail out if this is not a status-changing action
        if (strpos($action, 'flagship_shipping_') === false) {
            return;
        }

        console($action);
        die();

        $operation = substr($action, 18); // get the operation name from action

        if (!in_array($operation, array('pickup_schedule', 'pickup_void'))) {
            return;
        }

        $changed = 0;
        $post_ids = array_map('absint', (array) $_REQUEST['post']);
        $shippings = array();

        foreach ($post_ids as $post_id) {
            $order = wc_get_order($post_id);
            $shipment = get_post_meta($order->id, 'flagship_shipping_raw', true);

            $order->update_status($new_status, __('Order status changed by bulk edit:', 'woocommerce'), true);
            do_action('woocommerce_order_edit_status', $post_id, $new_status);
            ++$changed;
        }

        $sendback = add_query_arg(array('post_type' => 'shop_order', $report_action => true, 'changed' => $changed, 'ids' => implode(',', $post_ids)), '');

        if (isset($_GET['post_status'])) {
            $sendback = add_query_arg('post_status', sanitize_text_field($_GET['post_status']), $sendback);
        }

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

            $data[$shipment['service']['courier_name']][$order->id] = array(
                'shipment' => $shipment,
                'order' => $order,
            );
        }

        return $data;
    }
}

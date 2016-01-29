<?php

class WC_Meta_Box_Flagship_Shipping_Actions
{
    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output($post)
    {
        global $post, $thepostid, $theorder;

        if (!is_int($thepostid)) {
            $thepostid = $post->ID;
        }

        if (!is_object($theorder)) {
            $theorder = wc_get_order($thepostid);
        }

        $order = $theorder;

        $shipping_methods = $order->get_shipping_methods();
        $shipping = $shipping_methods[key($shipping_methods)];

        list($provider, $courier, $courier_code) = explode(':', $shipping['method_id']);

        if ($provider == FLAGSHIP_SHIPPING_PLUGIN_ID) {
            ?>
        <ul class="order_actions submitbox">
            <li class="wide">
            <?php
            woocommerce_wp_radio(array(
                'id' => 'flagship-shipping-service',
                'label' => __('Choose Service', 'flagship-shipping'),
                'options' => array(
                    strtolower($courier).':'.$courier_code => $courier.' - '.$courier_code.' $'.$shipping['cost'],
                ),
            ));
            ?>
            </li>
            <li class="wide">
                <buttpn type="submit" class="button save_order button-primary"><?php echo __('Create', 'flagship-shipping');
            ?></buttpn>
            </li>
        </ul>
        <?php

        }

        $data = get_post_meta($post->ID);
    }

    /**
     * Save meta box data.
     *
     * @param int     $post_id
     * @param WP_Post $post
     */
    public static function save($post_id, $post)
    {
        wc_save_order_items($post_id, $_POST);
    }
}

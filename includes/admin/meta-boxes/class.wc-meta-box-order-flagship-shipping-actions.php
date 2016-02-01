<?php

class WC_Meta_Box_Order_Flagship_Shipping_Actions
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

        $service = Flagship_Request_Formatter::get_flagship_shipping_service($order);

        $shipment = get_post_meta($thepostid, 'flagship_shipping_raw', true);

        if ($shipment) {
        ?>
        <ul>
            <li>
                <strong>Flagship ID:</strong> <?php echo $shipment['shipment_id']; ?>
                <br/>
                <strong>Service:</strong> <?php echo $shipment['service']['courier_name'].' - '.$shipment['service']['courier_desc'];?>
                <br/>
                <strong>Tracking Number:</strong> <?php echo $shipment['tracking_number'];?>
                <br/>
                <strong>Cost:</strong> $<?php echo $shipment['price']['total'];?>
                <hr/>
                <p>Print labels:</p>
                <a class="button button-primary" href="<?php echo $shipment['labels']['regular']; ?>">Regular label</a> <a class="button button-primary" href="<?php echo $shipment['labels']['thermal']; ?>">Thermal label</a>
                <hr/>
            </li>
            <li>
                <input type="hidden" name="flagship_shipping_void_shipment_id" value="<?php echo $shipment['shipment_id']; ?>"/>
                <button class="button" type="submit">Void Shipment</button>
            </li>
        </ul>    
        
        <?php
            return;
        }

        if ($service['provider'] == FLAGSHIP_SHIPPING_PLUGIN_ID) {
            ?>
        <ul class="order_actions submitbox">
            <li class="wide">
            <?php
            woocommerce_wp_select(array(
                'id' => 'flagship-shipping-service',
                'label' => __('Choose Service', 'flagship-shipping'),
                'name' => 'flagship_shipping_service',
                'options' => array(
                    $service['courier_name'].':'.$service['courier_code'] => ucfirst($service['courier_name']).' - '.$service['courier_code'].' $'.$shipping['cost'],
                ),
            ));
            ?>
            </li>
            <li class="wide">
                <button type="submit" class="button save_order button-primary">
            <?php
                    echo __('Create', 'flagship-shipping');
            ?>
                </button>
            </li>
        </ul>
        <?php

        } else {
        ?>
        Shipment was not quoted with Flagship Shipping.
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
        $order = wc_get_order($post_id);

        $shipment = get_post_meta($post_id, 'flagship_shipping_raw', true);

        // in case 
        if (empty($shipment)) {
            $flagship = Flagship_Application::get_instance();

            $response = $flagship->client()->post(
                '/ship/confirm',
                Flagship_Request_Formatter::get_confirmation_request($order)
            );

            $shipping = $response->get_content();

            if ($shipping['errors']) {
                return;
            }

            update_post_meta($post_id, 'flagship_shipping_shipment_id', $shipping['content']['shipment_id']);
            update_post_meta($post_id, 'flagship_shipping_shipment_tracking_number', $shipping['content']['tracking_number']);
            update_post_meta($post_id, 'flagship_shipping_courier_name', $shipping['content']['service']['courier_name']);
            update_post_meta($post_id, 'flagship_shipping_courier_service_code', $shipping['content']['service']['courier_code']);
            update_post_meta($post_id, 'flagship_shipping_raw', $shipping['content']);
            
            return;
        }

        $shipment_id = sanitize_text_field($_POST['flagship_shipping_void_shipment_id']);

        if (empty($shipment_id) || $shipment_id != $shipment['shipment_id']) {
            return;
        }

        $flagship = Flagship_Application::get_instance();
        $response = $flagship->client()->delete('/ship/shipments/'.$shipment_id);

        console('code is 204');
        console($response->get_code());
        console($response->get_code() == 204);

        if ($response->get_code() == 204) {
            delete_post_meta($post_id, 'flagship_shipping_raw');
        }



    }
}

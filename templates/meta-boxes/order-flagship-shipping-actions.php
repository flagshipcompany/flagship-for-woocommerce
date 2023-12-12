<?php use Automattic\WooCommerce\Utilities\OrderUtil; ?>
<input id="flagship-shipping-shipment-action" type="hidden" name="flagship_shipping_shipment_action"/>
<?php if ($type == 'created'): ?>
<input type="hidden" name="flagship_shipping_shipment_id" value="<?php echo $shipment['shipment_id']; ?>"/>
<ul>
    <li>
        <h4><?php _e('Summary', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></h4>
        <table class="wp-list-table widefat striped posts">
            <tr>
                <td><strong>FlagShip ID:</strong></td>
                <td><a href="https://smartship-ng.flagshipcompany.com/shipments/<?php echo $shipment['shipment_id']; ?>/overview"><?php echo $shipment['shipment_id']; ?></a></td>
            </tr>
            <tr>
                <td><strong><?php _e('Service', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</strong></td>
                <td><?php echo $shipment['service']['courier_name'].' - '.$shipment['service']['courier_desc']; ?></td>
            </tr>
            <tr>
                <td><strong><?php _e('Tracking Number', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</strong></td>
                <td><?php echo $shipment['tracking_number']; ?></td>
            </tr>
            <tr>
                <td><strong><?php _e('Cost', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</strong></td>
                <td>$<?php echo $shipment['price']['total']; ?></td>
            </tr>
        </table>
        <h4><?php _e('Print labels', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</h4>
        <table class="wp-list-table widefat striped posts">
            <tr>
                <td><?php _e('Regular label', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?><td>
                <td><a target="_blank" class="button button-primary" href="<?php echo $shipment['labels']['regular']; ?>"><?php _e('Print', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></a></td>
            </tr>
            <tr>
                <td><?php _e('Thermal label', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?><td>
                <td><a target="_blank" class="button button-primary" href="<?php echo $shipment['labels']['thermal']; ?>"><?php _e('Print', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></a></td>
            </tr>
            <?php if (isset($shipment['commercial_invoice'])): ?>
            <tr>
                <td><?php _e('Commercial invoice', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?><td>
                <td><a target="_blank" class="button button-primary" href="<?php echo $shipment['commercial_invoice']; ?>"><?php _e('Print', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></a></td>
            </tr>  
            <?php endif; ?>
        </table>
    </li>
    <li>
        <?php if (isset($shipment['pickup'])): ?>
        <h4><?php _e('Pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</h4>
        <strong><?php _e('Confirmation ID', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</strong> <?php echo $shipment['pickup']['id']; ?>
        <br/>
        <strong><?php _e('Date', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</strong> <?php echo $shipment['pickup']['date'].' '.$shipment['pickup']['from'].' - '.$shipment['pickup']['until']; ?>
        <br/>
        <button class="button flagship-shipping-action" data-shipment-action="pickup-void"><?php _e('Void pick-up'); ?></button>
        <hr/>
        <?php else: ?>
        <h4><?php _e('Request for pick-up', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</h4>
        <input type="date" name="flagship_shipping_pickup_schedule_date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+3 days')); ?>"/>
        <button id="flagship-shipping-pickup-schedule" class="button button-primary flagship-shipping-action" data-shipment-action="pickup-schedule"><?php _e('Schedule'); ?></button>
        <?php endif; ?>
    </li>
    <li>
        <h5><?php _e('Cancel Shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?> (<?php _e('use with caution', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>)</h5>
        <button class="button flagship-shipping-action" data-shipment-action="shipment-void"><?php _e('Void Shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    </li>
</ul>
<?php elseif ($type == 'exported'): ?>
    <p><?php _e('This order has already been exported to FlagShip: ', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?><a href="<?php echo $shipmentUrl; ?>" target=”_blank”><?php echo $exportedShipmentId; ?></a></p>
<?php elseif ($type == 'create'): ?>
    <p><?php _e('Client Choosen Rate', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</p>
    <?php
    woocommerce_wp_radio(array(
        'id' => 'flagship-shipping-service',
        'name' => 'flagship_shipping_service',
        'value' => $service['provider'].'|'.$service['courier_name'].'|'.$service['courier_code'].'|'.$service['courier_desc'].'|'.$service['date'].'|'.$service['instance_id'],
        'options' => array(
            $service['provider'].'|'.$service['courier_name'].'|'.$service['courier_code'].'|'.$service['courier_desc'].'|'.$service['date'].'|'.$service['instance_id'] => ucfirst($service['courier_name']).' - '.$service['courier_desc'],
        ),
        'label' => '',
    ));
    ?>
    <hr/>
    <?php if (isset($requote_rates)): ?>
    <p><?php _e('Requote Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</p>
    <?php
    woocommerce_wp_radio(array(
        'id' => 'flagship-shipping-service',
        'name' => 'flagship_shipping_service',
        'value' => $service['provider'].'|'.$service['courier_name'].'|'.$service['courier_code'].'|'.$service['courier_desc'].'|'.$service['date'].'|'.$service['instance_id'],
        'options' => $requote_rates,
        'label' => '',
    ));
    ?>
    <hr/>
    <?php endif; ?>
    <p><?php _e('Options', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</p>
    <?php

    global $post;
    if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
        $order = wc_get_order( $_GET['id'] );
        $billing_email = ($order->get_billing_email());
    } else {
        $post_meta = get_post_meta($post->ID);
        $billing_email = reset($post_meta['_billing_email']);
    }

    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_date',
        'name' => 'flagship_shipping_date',
        'value' => date('Y-m-d'),
        'type' => 'date',
        'label' => __('Shipping Date (Optional, default today):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
    ));

    woocommerce_wp_checkbox(array(
        'id' => 'flagship_shipping_enable_insurance',
        'name' => 'flagship_shipping_enable_insurance',
        'wrapper_class' => 'show_if_simple show_if_variable',
        'description' => __('Enable Insurance', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'label' => '',
    ));

    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_insurance_value',
        'name' => 'flagship_shipping_insurance_value',
        'label' => __('Insured items\' value (Required):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'wrapper_class' => 'show_if_simple show_if_variable hidden',
        'date_type' => 'price',
        'description' => __('<br/>$ value of the items to insure. Note that exlusions apply, see <a href="https://www.flagshipcompany.com/terms-and-conditions" target="_blank">here</a> for details', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
    ));

    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_insurance_description',
        'name' => 'flagship_shipping_insurance_description',
        'label' => __('Description (Required):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'wrapper_class' => 'show_if_simple show_if_variable hidden',
    ));

    woocommerce_wp_checkbox(array(
        'id' => 'flagship_shipping_signature_required',
        'name' => 'flagship_shipping_signature_required',
        'wrapper_class' => 'show_if_simple show_if_variable',
        'description' => __('Signature Required', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'label' => '',
        'value' => $signature_required,
    ));

    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_reference',
        'name' => 'flagship_shipping_reference',
        'label' => __('Reference (Optional):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
    ));
    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_driver_instructions',
        'name' => 'flagship_shipping_driver_instructions',
        'label' => __('Driver Instruction (Optional):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
    ));
    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_tracking_emails',
        'name' => 'flagship_shipping_tracking_emails',
        'label' => __('Tracking emails (Optional):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'value' => $billing_email,
        'custom_attributes' => array(
            'maxlength' => 100,
        ),
    ));

    ?>

    <button type="submit" class="button button-primary flagship-shipping-action" data-shipment-action="shipment-create"><?php _e('Create shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    <button type="submit" class="button flagship-shipping-action" data-shipment-action="shipment-requote"><?php _e('Requote', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    <button type="submit" class="button flagship-shipping-action" data-shipment-action="shipment-export"><?php _e('Export to FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
<script type="text/javascript">
(function($){
    $('#flagship_shipping_enable_insurance').click(function(){
        if($('#flagship_shipping_enable_insurance').is(':checked')) {
            $('.flagship_shipping_insurance_value_field').removeClass('hidden');
            $('.flagship_shipping_insurance_description_field').removeClass('hidden');
        } else {
            $('.flagship_shipping_insurance_value_field').addClass('hidden');
            $('.flagship_shipping_insurance_description_field').addClass('hidden');
        }
    });
})(jQuery);
</script>
<?php else: ?>
    <?php _e('Shipment was not quoted with FlagShip Shipping.', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>
    <hr/>
    <?php if (isset($requote_rates)): ?>
    <p><?php _e('Latest rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</p>
    <?php
    woocommerce_wp_radio(array(
        'id' => 'flagship_shipping_service',
        'name' => 'flagship_shipping_service',
        'label' => 'flagship_shipping_service',
        'options' => $requote_rates,
    ));
    ?>
    <hr/>
    <button type="submit" class="button button-primary flagship-shipping-action" data-shipment-action="shipment-create"><?php _e('Create shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    <button type="submit" class="button flagship-shipping-action" data-shipment-action="shipment-requote"><?php _e('Requote', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    <?php else: ?>
    <button type="submit" class="button button-primary flagship-shipping-action" data-shipment-action="shipment-requote"><?php _e('Get a quote', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>!</button>
    <?php endif; ?>
    <button type="submit" class="button flagship-shipping-action" data-shipment-action="shipment-export"><?php _e('Export to FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
<?php endif; ?>
<script type="text/javascript">
(function($){
    $('.flagship-shipping-action').click(function(e){
        $('#flagship-shipping-shipment-action').val($(this).attr('data-shipment-action'));
    });
})(jQuery);
</script>

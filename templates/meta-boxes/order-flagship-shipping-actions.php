<input id="flagship-shipping-shipment-action" type="hidden" name="flagship_shipping_shipment_action"/>
<?php if ($type == 'created'): ?>
<input type="hidden" name="flagship_shipping_shipment_id" value="<?php echo $shipment['shipment_id']; ?>"/>
<ul>
    <li>
        <h4><?php _e('Summary', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></h4>
        <table class="wp-list-table widefat striped posts">
            <tr>
                <td><strong>FlagShip ID:</strong></td>
                <td><a href="https://smartship.flagshipcompany.com/shipments/<?php echo $shipment['shipment_id']; ?>/overview"><?php echo $shipment['shipment_id']; ?></a></td>
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
        <input type="date" name="flagship_shipping_pickup_schedule_date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" miax="<?php echo date('Y-m-d', strtotime('+3 days')); ?>"/>
        <button id="flagship-shipping-pickup-schedule" class="button button-primary flagship-shipping-action" data-shipment-action="pickup-schedule"><?php _e('Schedule'); ?></button>
        <?php endif; ?>
    </li>
    <li>
        <h5><?php _e('Cancel Shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?> (<?php _e('use with caution', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>)</h5>
        <button class="button flagship-shipping-action" data-shipment-action="shipment-void"><?php _e('Void Shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    </li>
</ul>
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
    ));
    ?>
    <hr/>
    <?php endif; ?>
    <p><?php _e('Options', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>:</p>
    <?php
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
        'id' => 'flagship_shipping_enable_cod',
        'name' => 'flagship_shipping_enable_insurance',
        'wrapper_class' => 'show_if_simple show_if_variable',
        'description' => __('<abbr title="cash on delivery">Enable COD</abbr>', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'label' => '',
    ));

    woocommerce_wp_select(array(
        'id' => 'flagship_shipping_cod_method',
        'name' => 'flagship_shipping_cod_method',
        'options' => array(
            'check' => 'Check',
            'cert_check' => 'Certified Check',
            'money_order' => 'Money Order',
        ),
        'label' => 'Method (Required):',
        'wrapper_class' => 'show_if_simple show_if_variable hidden',
    ));

    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_cod_payable_to',
        'name' => 'flagship_shipping_cod_payable_to',
        'label' => __('Payable to (Required):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'wrapper_class' => 'show_if_simple show_if_variable hidden',
    ));

    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_cod_receiver_phone',
        'name' => 'flagship_shipping_cod_receiver_phone',
        'label' => __('Receiver Phone (Required):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'wrapper_class' => 'show_if_simple show_if_variable hidden',
    ));

    woocommerce_wp_text_input(array(
        'id' => 'flagship_shipping_cod_amount',
        'name' => 'flagship_shipping_cod_amount',
        'label' => __('Amount (Required):', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'wrapper_class' => 'show_if_simple show_if_variable hidden',
    ));

    woocommerce_wp_radio(array(
        'id' => 'flagship_shipping_cod_currency',
        'name' => 'flagship_shipping_cod_currency',
        'value' => $cod['currency'] == 'USD' ? 'USD' : 'CAD',
        'options' => array(
            'CAD' => 'Canadian Dollar',
            'USD' => 'U.S. Dollar',
        ),
        'label' => 'Currency (Required):',
        'wrapper_class' => 'show_if_simple show_if_variable hidden',
    ));

    woocommerce_wp_checkbox(array(
        'id' => 'flagship_shipping_signature_required',
        'name' => 'flagship_shipping_signature_required',
        'wrapper_class' => 'show_if_simple show_if_variable',
        'description' => __('Signature Required', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        'label' => '',
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

    ?>

    <button type="submit" class="button button-primary flagship-shipping-action" data-shipment-action="shipment-create"><?php _e('Create shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    <button type="submit" class="button flagship-shipping-action" data-shipment-action="shipment-requote"><?php _e('Requote', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
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

    $('#flagship_shipping_enable_cod').click(function(){
        if($('#flagship_shipping_enable_cod').is(':checked')) {
            $('.flagship_shipping_cod_method_field').removeClass('hidden');
            $('.flagship_shipping_cod_payable_to_field').removeClass('hidden');
            $('.flagship_shipping_cod_receiver_phone_field').removeClass('hidden');
            $('.flagship_shipping_cod_amount_field').removeClass('hidden');
            $('.flagship_shipping_cod_currency_field').removeClass('hidden');
        } else {
            $('.flagship_shipping_cod_method_field').addClass('hidden');
            $('.flagship_shipping_cod_payable_to_field').addClass('hidden');
            $('.flagship_shipping_cod_receiver_phone_field').addClass('hidden');
            $('.flagship_shipping_cod_amount_field').addClass('hidden');
            $('.flagship_shipping_cod_currency_field').addClass('hidden');
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
<?php endif; ?>
<script type="text/javascript">
(function($){
    $('.flagship-shipping-action').click(function(e){
        $('#flagship-shipping-shipment-action').val($(this).attr('data-shipment-action'));
    });

    $('button.button').click(function(e){
        // $(this).prop('disabled', true);
        if ($(this).data('clicked') !== undefined && $(this).data('clicked')) {
            $(this).prop('disabled', true);
        } else {
            $(this).data('clicked', true);
        }
    });
})(jQuery);
</script>

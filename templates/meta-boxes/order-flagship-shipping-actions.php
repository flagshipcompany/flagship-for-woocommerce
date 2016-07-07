<input id="flagship-shipping-shipment-action" type="hidden" name="flagship_shipping_shipment_action"/>
<?php if ($type == 'created'): ?>
<input type="hidden" name="flagship_shipping_shipment_id" value="<?php echo $shipment['shipment_id']; ?>"/>
<ul>
    <li>
        <h4>Summary</h4>
        <strong>Flagship ID:</strong> <?php echo $shipment['shipment_id']; ?>
        <br/>
        <strong>Service:</strong> <?php echo $shipment['service']['courier_name'].' - '.$shipment['service']['courier_desc'];?>
        <br/>
        <strong>Tracking Number:</strong> <?php echo $shipment['tracking_number'];?>
        <br/>
        <strong>Cost:</strong> $<?php echo $shipment['price']['total'];?>
        <hr/>
        <h4>Print labels:</h4>
        <a class="button button-primary" href="<?php echo $shipment['labels']['regular']; ?>"><?php echo __('Regular label', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></a> <a class="button button-primary" href="<?php echo $shipment['labels']['thermal']; ?>">Thermal label</a>
        <hr/>
    </li>
    <li>
        <?php if (isset($shipment['pickup'])): ?>
        <h4>Pick-up:</h4>
        <strong>Confirmation ID:</strong> <?php echo $shipment['pickup']['id']; ?>
        <br/>
        <strong>Date:</strong> <?php echo $shipment['pickup']['date'].' '.$shipment['pickup']['from'].' - '.$shipment['pickup']['until']; ?>
        <br/>
        <button class="button flagship-shipping-action" data-shipment-action="pickup-void"><?php echo __('Void pick-up');?></button>
        <hr/>
        <?php else: ?>
        <h4>Request for pick-up:</h4>
        <input type="date" name="flagship_shipping_pickup_schedule_date" value="<?php echo date('Y-m-d');?>" min="<?php echo date('Y-m-d');?>" miax="<?php echo date('Y-m-d', strtotime('+3 days'));?>"/>
        <button id="flagship-shipping-pickup-schedule" class="button button-primary flagship-shipping-action" data-shipment-action="pickup-schedule"><?php echo __('Schedule'); ?></button>
        <?php endif; ?>
    </li>
    <li>
        <h5>Cancel Shipment (use with caution)</h5>
        <button class="button flagship-shipping-action" data-shipment-action="shipment-void"><?php echo __('Void Shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></button>
    </li>
</ul>
<?php elseif ($type == 'create'): ?>
    <p>Client Choosen Rate:</p>
    <?php
    woocommerce_wp_radio(array(
        'id' => 'flagship-shipping-service',
        'name' => 'flagship_shipping_service',
        'value' => $service['provider'].'|'.$service['courier_name'].'|'.$service['courier_code'].'|'.$service['courier_desc'].'|'.$service['date'],
        'options' => array(
            $service['provider'].'|'.$service['courier_name'].'|'.$service['courier_code'].'|'.$service['courier_desc'].'|'.$service['date'] => ucfirst($service['courier_name']).' - '.$service['courier_desc'],
        ),
    ));
    ?>
    <hr/>
    <?php if (isset($requote_rates)): ?>
    <p>Requote Rates:</p>
    <?php
    woocommerce_wp_radio(array(
        'id' => 'flagship-shipping-service',
        'name' => 'flagship_shipping_service',
        'value' => $service['provider'].'|'.$service['courier_name'].'|'.$service['courier_code'].'|'.$service['courier_desc'].'|'.$service['date'],
        'options' => $requote_rates,
    ));
    ?>
    <hr/>
    <?php endif; ?>
    <p>Options:</p>
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
        'description' => __('Signatured Required', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
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

    <button type="submit" class="button button-primary flagship-shipping-action" data-shipment-action="shipment-create"><?php echo __('Create shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    <button type="submit" class="button flagship-shipping-action" data-shipment-action="shipment-requote">Requote</button>
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
    <?php echo __('Shipment was not quoted with Flagship Shipping.', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>
    <hr/>
    <?php if (isset($requote_rates)): ?>
    <p>Latest rates:</p>
    <?php
    woocommerce_wp_radio(array(
        'name' => 'flagship_shipping_service',
        'options' => $requote_rates,
    ));
    ?>
    <hr/>
    <button type="submit" class="button button-primary flagship-shipping-action" data-shipment-action="shipment-create"><?php echo __('Create shipment', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></button>
    <button type="submit" class="button flagship-shipping-action" data-shipment-action="shipment-requote">Requote</button>
    <?php else: ?>
    <button type="submit" class="button button-primary flagship-shipping-action" data-shipment-action="shipment-requote">Get a quote!</button>
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

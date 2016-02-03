<?php if ($type == 'created'): ?>
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
        <a class="button button-primary" href="<?php echo $shipment['labels']['regular']; ?>"><?php echo __('Regular label', 'flagship-shipping');?></a> <a class="button button-primary" href="<?php echo $shipment['labels']['thermal']; ?>">Thermal label</a>
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
        <input type="hidden" name="flagship_shipping_shipment_id" value="<?php echo $shipment['shipment_id']; ?>"/>
        <input id="flagship-shipping-shipment-action" type="hidden" name="flagship_shipping_shipment_action"/>
        <button class="button flagship-shipping-action" data-shipment-action="shipment-void"><?php echo __('Void Shipment', 'flagship-shipping');?></button>
    </li>
</ul>
<script type="text/javascript">
(function($){
    $(function(){
        $('.flagship-shipping-action').click(function(e){
            $('#flagship-shipping-shipment-action').val($(this).attr('data-shipment-action'));
        });
    });
})(jQuery);
</script>
<?php elseif ($type == 'create'): ?>
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
        <?php echo __('Create shipment', 'flagship-shipping'); ?>
        </button>
    </li>
</ul>
<?php else: ?>
<?php echo __('Shipment was not quoted with Flagship Shipping.', 'flagship-shipping'); ?>
<?php endif; ?>
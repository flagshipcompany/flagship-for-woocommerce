<?php if ($type == 'token') :?>
<div class="notice notice-warning is-dismissible">
	<p>
		<?php esc_attr_e('Set your Flagship Shipping token.', 'flagship-shipping'); ?> <?php Flagship_Html::anchor_e('flagship_shipping_settings', 'click here', array('escape' => true)); ?>
	</p>
</div>
<?php endif; ?>

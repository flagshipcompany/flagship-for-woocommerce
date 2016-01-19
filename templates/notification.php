<?php foreach ($notifications as $type => $messages): ?>
<div class="notice notice-<?php echo $type; ?> is-dismissible">
	<?php foreach ($messages as $message): ?>
	<p>
		<?php if (is_array($message)): ?>
			<?php foreach($message as $m): ?>
				<strong><?php echo __($m, 'flagship-shipping');?>.</strong><br/>
			<?php endforeach; ?>
		<?php else: ?>
			<strong><?php echo $message; ?></strong>
		<?php endif; ?>
	</p>
	<?php endforeach; ?>
</div>
<?php endforeach; ?>

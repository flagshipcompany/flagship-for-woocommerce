<tr valign="top" id="fcs-box-split-tr" data-field-name="<?php echo $field_name; ?>" data-weight-field-name="<?php echo $split_weight_field_name; ?>" data-package-box-field-name="<?php echo $packing_box_field_name; ?>">
	<th scope="row" class="titledesc">
		<?php echo $tooltip_html; ?>
		<label for="<?php echo $field_name; ?>"><?php _e('Box Split', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></label>
	</th>
	<td class="forminp">
		<fieldset>
			<legend class="screen-reader-text"><span><?php _e('Box Split', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></span></legend>
			<?php
				foreach ( $options as $key => $val ) {
				   ?>
				   <label for="<?php echo $field_name; ?>">
					<input class="" type="radio" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" style="" value="<?php echo $key; ?>" <?php if ($key === $box_split_option) { echo 'checked'; } ?>><?php echo $val; ?>
				</label>
				   <?php 
				}
			?>
			<br>
		</fieldset>
	</td>
    <script type="text/javascript">
        (function($, window){
            $(function(){
	            var fieldName = $('#fcs-box-split-tr').data('field-name');
	            var splitWeightFieldName = $('#fcs-box-split-tr').data('weight-field-name');

	            var toggleSplitWeight = function () {
				    if ('no' == $('input[name="' + fieldName + '"]:checked').val()) {
	                    $('input[name="' + splitWeightFieldName + '"]').closest('tr').show();
		            } else {
		            	$('input[name="' + splitWeightFieldName + '"]').closest('tr').hide();
		            }
				};

                var togglePackageBox = function () {
				    if ('packing' == $('input[name="' + fieldName + '"]:checked').val()) {
	                    $('#package_box_collection').closest('tr').show();
		            } else {
		            	$('#package_box_collection').closest('tr').hide();
		            }
				};

			    $('input[name="' + fieldName + '"]').change(function(){
	                toggleSplitWeight();
	                togglePackageBox();
	            });

	            toggleSplitWeight();
	            togglePackageBox();
            });
        })(jQuery, window);
    </script>
</tr>


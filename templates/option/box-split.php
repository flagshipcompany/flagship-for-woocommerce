<tr valign="top" id="fcs-box-split-tr" data-field-name="<?php echo $fieldName; ?>" data-weight-field-name="<?php echo $splitWeightFieldName; ?>" data-packing-field-name="<?php echo $packingFieldName; ?>">
	<th scope="row" class="titledesc">
		<label for="<?php echo $fieldName; ?>"><?php _e('Box Split', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></label>
	</th>
	<td class="forminp">
		<fieldset>
			<legend class="screen-reader-text"><span><?php _e('Box Split', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></span></legend>
			<label for="<?php echo $fieldName; ?>">
				<input class="" type="radio" name="<?php echo $fieldName; ?>" id="<?php echo $fieldName; ?>" style="" value="yes" <?php if ('yes' === $sameBox) { echo 'checked'; } ?>><?php _e('Everything in one package box', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>
			</label>
			<label for="<?php echo $fieldName; ?>">
				<input class="" type="radio" name="<?php echo $fieldName; ?>" id="<?php echo $fieldName; ?>" style="" value="no" <?php if ('no' === $sameBox) { echo 'checked'; } ?>><?php _e('Split by weight', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>
			</label>
			<br>
		</fieldset>
	</td>
    <script type="text/javascript">
        (function($, window){
            $(function(){
	            var fieldName = $('#fcs-box-split-tr').data('field-name');
	            var splitWeightFieldName = $('#fcs-box-split-tr').data('weight-field-name');
	            var packingFieldName = $('#fcs-box-split-tr').data('packing-field-name');
	            var toggleSplitWeight = function () {
				    if ('yes' == $('input[name="' + fieldName + '"]:checked').val()) {
	                    $('input[name="' + splitWeightFieldName + '"]').closest('tr').hide();
		            } else {
		            	$('input[name="' + splitWeightFieldName + '"]').closest('tr').show();
		            }
				};

			    $('input[name="' + fieldName + '"]').change(function(){
	                toggleSplitWeight();
	            });

	           $('input[name="' + packingFieldName + '"]').change(function(){
	                if ($(this).prop('checked')) {
	                	$('#fcs-box-split-tr').hide();
                        $('input[name="' + splitWeightFieldName + '"]').closest('tr').hide();
	                } else {
	                	$('#fcs-box-split-tr').show();
                        toggleSplitWeight();
	                }
	            });

				if ($('input[name="' + packingFieldName + '"]').prop('checked')) {
                   $('#fcs-box-split-tr').hide();
                   $('input[name="' + splitWeightFieldName + '"]').closest('tr').hide();

                   return;
                }

	            toggleSplitWeight();
            });
        })(jQuery, window);
    </script>
</tr>


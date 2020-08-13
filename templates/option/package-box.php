<tr valign="top">
    <th scope="row" class="titledesc"><?php _e('Package Box', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
?>:</th>
    <td class="forminp" id="package_box_collection">
        <table class="widefat wc_input_table sortable" cellspacing="0">
            <thead>
                <tr>
                    <th class="sort">&nbsp;</th>
                    <th class="package_box_header_col required_header"><?php _e('Model Name', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></th>
                    <th colspan="2" class="package_box_header_col"><?php _e('Length (in)', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th colspan="2" class="package_box_header_col"><?php _e('Width (in)', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></th>
                    <th colspan="2" class="package_box_header_col"><?php _e('Height (in)', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></th>
                    <th colspan="2" class="package_box_header_col"><?php _e('Weight (LB)', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></th>
                    <th class="package_box_header_col"><?php _e('Markup ($)', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>
                        <?php echo wc_help_tip(__('If set, an additional fee will be charged on each package using this model of box', FLAGSHIP_SHIPPING_TEXT_DOMAIN)); ?>
                    </th>
                    <th class="package_box_header_col"><?php _e('Shipping classes', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>
                        <?php echo wc_help_tip(__('If specified, the box will be used ONLY when each item in the order belongs to one of the specified shipping classes', FLAGSHIP_SHIPPING_TEXT_DOMAIN)); ?>
                    </th>
                </tr>
                <tr>
                    <th class="sort">&nbsp;</th>
                    <th class="package_box_header_col"></th>
                    <th class="package_box_header_col required_header"><?php _e('Outer', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th class="package_box_header_col"><?php _e('Inner', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th class="package_box_header_col required_header"><?php _e('Outer', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th class="package_box_header_col"><?php _e('Inner', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th class="package_box_header_col required_header"><?php _e('Outer', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th class="package_box_header_col"><?php _e('Inner', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th class="package_box_header_col required_header"><?php _e('Supported', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th class="package_box_header_col"><?php _e('Empty', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th class="package_box_header_col"></th>
                    <th class="package_box_header_col"></th>
                </tr>
            </thead>
            <tbody class="accounts">
            <?php
            $i = -1;
            if ($packageBoxes) {
                foreach ($packageBoxes as $box) {
                    ++$i;

                    $markup = isset($box['markup']) ? $box['markup'] : null;
                    $shippingClasses = isset($box['shipping_classes']) ? $box['shipping_classes'] : null;

                    echo '<tr class="package_box">
                            <td class="sort"></td>
                            <td><input type="text" value="'.esc_attr(wp_unslash($box['model_name'])).'" name="package_box_model_name['.$i.']" /></td>
                            <td><input type="number" value="'.esc_attr($box['length']).'" name="package_box_length['.$i.']" style="min-width: 80px" /></td>
                            <td><input type="number" value="'.esc_attr($box['inner_length']).'" name="package_box_inner_length['.$i.']" style="min-width: 80px" /></td>
                            <td><input type="number" value="'.esc_attr($box['width']).'" name="package_box_width['.$i.']" style="min-width: 80px" /></td>
                            <td><input type="number" value="'.esc_attr($box['inner_width']).'" name="package_box_inner_width['.$i.']" style="min-width: 80px" /></td>
                            <td><input type="number" value="'.esc_attr($box['height']).'" name="package_box_height['.$i.']" style="min-width: 80px" /></td>
                            <td><input type="number" value="'.esc_attr($box['inner_height']).'" name="package_box_inner_height['.$i.']" style="min-width: 80px" /></td>
                            <td><input type="number" value="'.esc_attr($box['max_weight']).'" name="package_box_max_weight['.$i.']" style="min-width: 80px" /></td>
                            <td><input type="number" value="'.esc_attr($box['weight']).'" name="package_box_weight['.$i.']" style="min-width: 80px" /></td>
                            <td><input type="number" value="'.esc_attr($markup).'" name="package_box_markup['.$i.']" style="min-width: 80px" step="0.01" /></td>
                            <td><input type="text" value="'.esc_attr($shippingClasses).'" name="package_box_shipping_classes['.$i.']" style="min-width: 80px;" disabled="disabled"/></td>
                        </tr>
                        ';
                }
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="12">
                        <a href="#" class="add button">
                            <?php _e('+ Add package box', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>
                        </a>&nbsp;
                        <a href="#" class="remove_rows button">
                            <?php _e('Remove selected package box(es)', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?>
                        </a>
                    </th>
                </tr>
            </tfoot>
        </table>
        <p class="description">
            <?php _e('Supported weight is the maximum supported weight. Empty weight is the weight of an empty box.', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?>
        </p>
        <?php
        $shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );
        foreach ($shipping_classes as $shipping_class) {
            $shippingClassesOptions[] = [
                "name" => $shipping_class->name
            ];
        }
        ?>
        <script type="text/javascript">
            (function($, window){
                $(function(){
                    $('#package_box_collection').on('click', 'a.add', function(){
                        var size = $('#package_box_collection').find('tbody .package_box').length;
                        var shippingClassesOptions = <?php echo json_encode($shippingClassesOptions); ?>;
                        var shippingClassesDropdown = '';
                        shippingClassesOptions.forEach(function(item,index){
                            shippingClassesDropdown += '<option value="'+item.name+'">'+item.name+'</option>';
                        });

                        $('<tr class="package_box">\
                                <td class="sort"></td>\
                                <td><input type="text" name="package_box_model_name[' + size + ']" /></td>\
                                <td><input class="outer_dim" type="number" name="package_box_length[' + size + ']" style="min-width: 80px" /></td>\
                                <td><input type="number" name="package_box_inner_length[' + size + ']" style="min-width: 80px" /></td>\
                                <td><input type="number" class="outer_dim" name="package_box_width[' + size + ']" style="min-width: 80px" /></td>\
                                <td><input type="number" name="package_box_inner_width[' + size + ']" style="min-width: 80px" /></td>\
                                <td><input type="number" class="outer_dim" name="package_box_height[' + size + ']" style="min-width: 80px" /></td>\
                                <td><input type="number" name="package_box_inner_height[' + size + ']" style="min-width: 80px" /></td>\
                                <td><input type="number" name="package_box_max_weight[' + size + ']" style="min-width: 80px" /></td>\
                                <td><input type="number" name="package_box_weight[' + size + ']" style="min-width: 80px" /></td>\
                                <td><input type="number" step="0.01" name="package_box_markup[' + size + ']" style="min-width: 80px" /></td>\
                                <td><select name="package_box_shipping_classes['+size+']" style="min-width:80px;">'+shippingClassesDropdown+'</select></td>\
                        </tr>').appendTo('#package_box_collection table tbody');

                        return false;
                    });

                    $('#package_box_collection').on('click', 'a.remove_rows', function(e){
                        $('#package_box_collection').find('.package_box_selected:checked').each(function() {
                            $(this).closest('tr.package_box').remove();
                        });

                        return false;
                    });
                });
            })(jQuery, window);
        </script>
        <style type="text/css">
            .package_box_header_col {
                border: 1px solid;
                border-color: rgb(223, 223, 223);
            }
            .required_header:before {
              content:"*";
              color:red;
            }
        </style>
    </td>
</tr>

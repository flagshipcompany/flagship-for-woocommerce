<tr valign="top">
    <th scope="row" class="titledesc"><?php _e('Package Box', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
?>:</th>
    <td class="forminp" id="package_box_collection">
        <table class="widefat wc_input_table sortable" cellspacing="0">
            <thead>
                <tr>
                    <th class="sort">&nbsp;</th>
                    <th><?php _e('Model Name', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></th>
                    <th><?php _e('Length (in)', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th><?php _e('Width (in)', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></th>
                    <th><?php _e('Height (in)', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></th>
                    <th><?php _e('Weight (LB)', FLAGSHIP_SHIPPING_TEXT_DOMAIN);?></th>
                    <th><?php _e('Max. Supported (LB)', FLAGSHIP_SHIPPING_TEXT_DOMAIN); ?></th>
                </tr>
            </thead>
            <tbody class="accounts">
            <?php
            $i = -1;
            if ($packageBoxes) {
                foreach ($packageBoxes as $box) {
                    ++$i;

                    echo '<tr class="account">
                                        <td class="sort"></td>
                                        <td><input type="text" value="'.esc_attr(wp_unslash($box['model_name'])).'" name="package_box_model_name['.$i.']" /></td>
                                        <td><input type="text" value="'.esc_attr($box['length']).'" name="package_box_length['.$i.']" /></td>
                                        <td><input type="text" value="'.esc_attr(wp_unslash($box['width'])).'" name="package_box_width['.$i.']" /></td>
                                        <td><input type="text" value="'.esc_attr($box['height']).'" name="package_box_height['.$i.']" /></td>
                                        <td><input type="text" value="'.esc_attr($box['weight']).'" name="package_box_weight['.$i.']" /></td>
                                        <td><input type="text" value="'.esc_attr($box['max_weight']).'" name="package_box_max_weight['.$i.']" /></td>
                                    </tr>';
                }
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7">
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
        <script type="text/javascript">
            (function($, window){
                $(function(){
                    $('#package_box_collection').on('click', 'a.add', function(){
                        var size = $('#package_box_collection').find('tbody .account').length;

                        $('<tr class="package_box">\
                                <td class="sort"></td>\
                                <td><input type="text" name="package_box_model_name[' + size + ']" /></td>\
                                <td><input type="text" name="package_box_length[' + size + ']" /></td>\
                                <td><input type="text" name="package_box_width[' + size + ']" /></td>\
                                <td><input type="text" name="package_box_height[' + size + ']" /></td>\
                                <td><input type="text" name="package_box_weight[' + size + ']" /></td>\
                                <td><input type="text" name="package_box_max_weight[' + size + ']" /></td>\
                            </tr>').appendTo('#package_box_collection table tbody');

                        return false;
                    });
                });
            })(jQuery, window);
        </script>
    </td>
</tr>
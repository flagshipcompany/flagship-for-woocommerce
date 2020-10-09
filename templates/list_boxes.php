
<?php
    wp_enqueue_script('vuejs');
    wp_enqueue_script('package_boxes');
    wp_enqueue_style('flagship_style');

    $shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );
?>

<div id="flagship_package_boxes">
    <span class="hidden" ref="getBoxesUrl"><?php  echo $get_boxes_url; ?></span>
    <span class="hidden" ref="saveBoxesUrl"><?php  echo $save_boxes_url; ?></span>
    <div class="woocommerce-layout">
        <div class="woocommerce-layout__header">
            <h1 class="woocommerce-layout__header-breadcrumbs"></h1>
        </div>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('Package boxes','flagship-for-woocommerce'); ?></h1>
            <div v-show="data_saved" id="saved_message" class="notice notice-success"><p><strong><?php _e('Boxes have been saved.','flagship-for-woocommerce'); ?></strong></p></div>
            <div v-show="invalid_data" id="error_message" class="notice notice-error"><p><strong><?php _e('Invalid submission. Please check all the fields.', 'flagship-for-woocommerce'); ?></strong></p></div>
            <div v-show="save_error" id="error_message" class="notice notice-error"><p><strong><?php _e('Saving failed.', 'flagship-for-woocommerce'); ?></strong></p></div>
            <table class="wp-list-table widefat fixed striped posts packing_boxes_table">
                <thead>
                  <tr>
                      <th colspan="2"><strong><?php _e('Model name','flagship-for-woocommerce'); ?></strong></th>
                      <th colspan="2"><strong><?php _e('Length (in)','flagship-for-woocommerce'); ?></strong></th>
                      <th colspan="2"><strong><?php _e('Width (in)','flagship-for-woocommerce'); ?></strong></th>
                      <th colspan="2"><strong><?php _e('Height (in)','flagship-for-woocommerce'); ?></strong></th>
                      <th colspan="2"><strong><?php _e('Weight (LB)','flagship-for-woocommerce'); ?></strong></th>
                      <th colspan="2"><strong><?php _e('Extra charge ($) (optional)','flagship-for-woocommerce'); ?></strong></th>
                      <th colspan="2"><strong><?php _e('Shipping Class (optional)','flagship-for-woocommerce'); ?></strong></th>
                      <th></th>
                  </tr>
                  <tr>
                      <th colspan="2"></th>
                      <th class="required_header"><?php _e('Outer','flagship-for-woocommerce'); ?></th>
                      <th><?php _e('Inner','flagship-for-woocommerce'); ?></th>
                      <th class="required_header"><?php _e('Outer','flagship-for-woocommerce'); ?></th>
                      <th><?php _e('Inner','flagship-for-woocommerce'); ?></th>
                      <th class="required_header"><?php _e('Outer','flagship-for-woocommerce'); ?></th>
                      <th><?php _e('Inner','flagship-for-woocommerce'); ?></th>
                      <th class="required_header" nowrap><?php _e('Supported','flagship-for-woocommerce'); ?></th>
                      <th><?php _e('Empty','flagship-for-woocommerce'); ?></th>
                      <th colspan="2"></th>
                      <th colspan="2"></th>
                      <th></th>
                  </tr>
                </thead>
                <tbody>
                    <tr v-for="(box, index) in boxes" v-bind:key="index" ref="box_list">
                        <td colspan="2"><input v-model.trim="box.model" v-bind:name=" 'flagship_boxes' + index + '_model' " v-bind:id=" 'flagship_boxes' + index + '_model' " type="text" style="width:100px;"></td>
                        <td><input v-model.number="box.length" v-bind:name=" 'flagship_boxes' + index + '_length' " v-bind:id=" 'flagship_boxes' + index + '_length' " type="text" style="width:50px;"></td>
                        <td><input v-model.number="box.inner_length" v-bind:name=" 'flagship_boxes' + index + '_inner_length' " v-bind:id=" 'flagship_boxes' + index + '_inner_length' " type="text" style="width:50px;"></td>
                        <td><input v-model.number="box.width" v-bind:name=" 'flagship_boxes' + index + '_width' " v-bind:id=" 'flagship_boxes' + index + '_width' " type="text" style="width:50px;"></td>
                        <td><input v-model.number="box.inner_width" v-bind:name=" 'flagship_boxes' + index + '_inner_width' " v-bind:id=" 'flagship_boxes' + index + '_inner_width' " type="text" style="width:50px;"></td>
                        <td><input v-model.number="box.height" v-bind:name=" 'flagship_boxes' + index + '_height' " v-bind:id=" 'flagship_boxes' + index + '_height' " type="text" style="width:50px;"></td>
                        <td><input v-model.number="box.inner_height" v-bind:name=" 'flagship_boxes' + index + '_inner_height' " v-bind:id=" 'flagship_boxes' + index + '_inner_height' " type="text" style="width:50px;"></td>
                        <td><input v-model.number="box.max_weight" v-bind:name=" 'flagship_boxes' + index + '_max_weight' " v-bind:id=" 'flagship_boxes' + index + '_max_weight' " type="text" style="width:50px;"></td>
                        <td><input v-model.number="box.weight" v-bind:name=" 'flagship_boxes' + index + '_weight' " v-bind:id=" 'flagship_boxes' + index + '_weight' " type="text" style="width:50px;"></td>
                        <td colspan="2"><input v-model.number="box.extra_charge" v-bind:name=" 'flagship_boxes' + index + '_extra_charge' " v-bind:id=" 'flagship_boxes' + index + '_extra_charge' " type="text" style="width:100px;"></td>
                        <td colspan="2"><input v-model.trim="box.shipping_class" v-bind:name=" 'flagship_boxes' + index + '_shipping_class'" v-bind:id=" 'flagship_boxes' + index + '_shipping_class' " type="text" style="width:100px;" disabled="disabled"></td>
                        <td><button v-on:click="removeBox(box.id)" v-bind:name=" 'remove_' + box.id" class="button-link-delete button"><?php _e('Remove'); ?></button></td>
                    </tr>
                    <tr ref="box_new">
                        <td colspan="2"><input v-model.trim="box_form.model" name="flagship_boxes_new_model" id="flagship_boxes_new_model" type="text" style="width:100px;"></td>
                        <td><input v-model.number="box_form.length" name="flagship_boxes_new_length" id="flagship_boxes_new_length" type="text" style="width:50px;"></td>
                        <td><input v-model.number="box_form.inner_length" name="flagship_boxes_new_inner_length" id="flagship_boxes_new_inner_length" type="text" style="width:50px;"></td>
                        <td><input v-model.number="box_form.width" name="flagship_boxes_new_width" id="flagship_boxes_new_width" type="text" style="width:50px;"></td>
                        <td><input v-model.number="box_form.inner_width" name="flagship_boxes_new_inner_width" id="flagship_boxes_new_inner_width" type="text" style="width:50px;"></td>
                        <td><input v-model.number="box_form.height" name="flagship_boxes_new_height" id="flagship_boxes_new_height" type="text" style="width:50px;"></td>
                        <td><input v-model.number="box_form.inner_height" name="flagship_boxes_new_inner_height" id="flagship_boxes_new_inner_height" type="text" style="width:50px;"></td>
                        <td><input v-model.number="box_form.max_weight" name="flagship_boxes_new_max_weight" id="flagship_boxes_new_max_weight" type="text" style="width:50px;"></td>
                        <td><input v-model.number="box_form.weight" name="flagship_boxes_new_weight" id="flagship_boxes_new_weight" type="text" style="width:50px;"></td>
                        <td colspan="2"><input v-model.number="box_form.extra_charge" name="flagship_boxes_new_extra_charge" id="flagship_boxes_new_extra_charge" type="text" style="width:100px;"></td>
                        <td colspan="2"><select v-model.trim="box_form.shipping_class" name="flagship_boxes_new_shipping_class" id="flagship_boxes_new_shipping_class" style="width:100px">
                            <?php foreach ($shipping_classes as $shipping_class) { ?>
                                <option value="<?php echo $shipping_class->name; ?>"><?php echo $shipping_class->name; ?></option>
                            <?php } ?>
                        </select></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
              <button v-on:click="saveBoxes()" name="save" class="button-primary woocommerce-save-button" type="submit"><?php _e('Save'); ?></button>
            </p>
            <div>
              <p>
                <?php _e('Note: When calculating shipping rates, if no box is set up or no suitable boxes are available for items, all the items will be placed in one single package for the calculation.','flagship-for-woocommerce'); ?>
              </p>
            </div>
        </div>
    </div>
</div>

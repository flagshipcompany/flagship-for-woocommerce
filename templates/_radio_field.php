<?php $value = isset($value) ? $value : $default; ?>

<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo $field_name; ?>"><?php echo $title; ?></label>
    </th>
    <td class="forminp">
        <fieldset>
            <legend class="screen-reader-text"><span><?php esc_html_e('Box split', 'flagship-for-woocommerce'); ?></span></legend>
              <?php
                foreach ( $options as $key => $val ) {
                   ?>
                      <label for="<?php echo $field_name; ?>">
                        <input class="" type="radio" name="<?php echo $field_name; ?>" style="" value="<?php echo $key; ?>" <?php if ($key === $value) echo 'checked'; ?> ><?php echo _e($val,'flagship-for-woocommerce'); if (isset($extra_note[$key])) echo ' '.$extra_note[$key]; ?>
                      </label>
                   <?php 
                }
              ?> 
        </fieldset>
    </td>
</tr>
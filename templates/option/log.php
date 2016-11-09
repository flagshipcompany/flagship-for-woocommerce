<tr valign="top">
            <th scope="row" class="titledesc">
                <?php echo wp_kses_post($data['title']);
        ?>
            </th>
            <td class="forminp">
                <input type="hidden" 
                    id="<?php echo esc_attr($field_key);
        ?>"
                    name="<?php echo esc_attr($field_key);
        ?>"
                    value=""
                />
        <?php if ($logs) : ?>
                <table class="wc_gateways widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php _e('Timestamp', FLAGSHIP_SHIPPING_TEXT_DOMAIN) ?></th>
                            <th><?php _e('Log', FLAGSHIP_SHIPPING_TEXT_DOMAIN) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log) :?>
                        <tr>
                            <td width="20%"><?php echo date('Y-m-d H:i:s', $log['timestamp']);
        ?></td>
                            <td><?php $ctx->getComponent('\\FS\\Components\\Html')->ul_e($log['log']);
        ?></td>
                        </tr>
                        <?php endforeach;
        ?>
                    </tbody>
                </table>
                <?php echo $description;
        ?>
        <?php endif;
        ?>
            </td>
        </tr>
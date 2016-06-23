<?php

class Flagship_Url
{
    public function make($name, $escape = false)
    {
        $args = array();
        $base_url;

        switch ($name) {
            case 'flagship_shipping_settings':
                $args['page'] = version_compare(WC()->version, '2.1', '>=') ? 'wc-settings' : 'woocommerce_settings';
                $args['tab'] = 'shipping';
                $args['section'] = 'flagship_wc_shipping_method';
                $base_url = admin_url('admin.php');
                break;
            default:
                return false;
        }

        $url = add_query_arg($args, $base_url);

        if (!$escape) {
            return $url;
        }

        return esc_url($url);
    }
}

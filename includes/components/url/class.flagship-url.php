<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Url extends Flagship_Component
{
    public function make($name, $escape = false)
    {
        $args = array();
        $base_url;

        $page = version_compare(WC()->version, '2.1', '>=') ? 'wc-settings' : 'woocommerce_settings';

        switch ($name) {
            case 'flagship_shipping_settings':
                $args['page'] = $page;
                $args['tab'] = 'shipping';
                $args['section'] = 'flagship_wc_shipping_method';
                $base_url = admin_url('admin.php');
                break;
            case 'wc_tax_settings':
                $args['page'] = $page;
                $args['tab'] = 'tax';
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

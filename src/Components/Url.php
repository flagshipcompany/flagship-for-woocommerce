<?php

namespace FS\Components;

class Url extends \FS\Components\AbstractComponent
{
    public function make($name, $escape = false)
    {
        $args = array();
        $base_url;

        $page = \version_compare(WC()->version, '2.1', '>=') ? 'wc-settings' : 'woocommerce_settings';
        $isVer26OorNewer = \version_compare(WC()->version, '2.6', '>=');

        switch ($name) {
            case 'flagship_shipping_settings':
                $args['page'] = $page;
                $args['tab'] = 'shipping';
                $args['section'] = $isVer26OorNewer ? $this->getApplicationContext()->setting('FLAGSHIP_SHIPPING_PLUGIN_ID') : 'flagship_wc_shipping_method';
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

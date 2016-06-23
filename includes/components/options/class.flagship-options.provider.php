<?php

require_once __DIR__.'/class.flagship-options.php';

class Flagship_Options_Provider
{
    public function provide(Flagship_Application $flagship)
    {
        $flagship['options'] = new Flagship_Options(
            get_option('woocommerce_'.FLAGSHIP_SHIPPING_PLUGIN_ID.'_settings', array())
        );
    }
}

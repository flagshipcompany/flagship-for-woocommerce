<?php

class Flagship_Test_Installer
{
    public function install()
    {
        $settings = include_once __DIR__.'/../data/flagship_plugin_settings.php';

        update_option('woocommerce_flagship_shipping_method_settings', $settings);
    }
}

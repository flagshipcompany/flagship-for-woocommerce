<?php

/*
 * Plugin Name: Flagship WooCommerce Shipping
 * Plugin URI: http://www.smartship.io/woo-commerce
 * Description: An e-shipping courier solution that helps you shipping anything from Canada. Beautifully. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="http://smartship.flagshipcompany.com/">Sign up for an Smartship account</a> to get an API key, and 3) Go to your Akismet configuration page, and save your API key.
 * Version: 1.0.0
 * Author: Flagship Courier Solution
 * Author URI: http://www.smartship.io/woo-commerce
 * Requires at least: 4.4
 * Tested up to: 4.4
 *
 * Text Domain: smartship.io
 * Domain Path: /i18n/languages/
 *
 * Copyright: © 2016 Flagship Courier Solution.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// prevent data leak
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('FLS__PLUGIN_URL', plugin_dir_url(__FILE__));
define('FLS__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FLS__PLUGIN_BASENAME', plugin_basename(__FILE__));
define('FLAGSHIP_SHIPPING_PLUGIN_ID', 'flagship_shipping_method');
define('FLAGSHIP_NAME_PREFIX', 'flagship_');

if (!function_exists('console')) {
    function console($var)
    {
        $home = exec('echo ~');
        $text = $var;
        if (!is_string($var)) {
            ob_start();
            var_dump($var);
            $text = strip_tags(ob_get_clean());
        }
        file_put_contents($home.'/Desktop/data', date('Y-m-d H:i:s')."\t".print_r($text, 1)."\n", FILE_APPEND | LOCK_EX);
    }
}

/*
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    //
    require_once FLS__PLUGIN_DIR.'includes/class.flagship-application.php';

    Flagship_Application::init(
        get_option('woocommerce_'.FLAGSHIP_SHIPPING_PLUGIN_ID.'_settings', array()),
        is_admin()
    );
}

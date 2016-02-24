<?php

/*
 * Plugin Name: Flagship WooCommerce Shipping
 * Plugin URI: http://www.smartship.io/woo-commerce
 * Description: An e-shipping courier solution that helps you shipping anything from Canada. Beautifully. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="http://smartship.flagshipcompany.com/">Sign up for an Smartship account</a> to get an API key, and 3) Go to <a href="/wp-admin/admin.php?page=wc-settings&tab=shipping&section=flagship_wc_shipping_method">settings</a> page to fill basic shipping credentials
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

define('FLAGSHIP_SHIPPING_API_ENTRY_POINT', 'http://127.0.0.1:3002');
define('FLAGSHIP_SHIPPING_API_TIMEOUT', 14);

// Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    //
    require_once FLS__PLUGIN_DIR.'includes/class.flagship-application.php';

    Flagship_Application::init(
        get_option('woocommerce_'.FLAGSHIP_SHIPPING_PLUGIN_ID.'_settings', array()),
        is_admin()
    );
}

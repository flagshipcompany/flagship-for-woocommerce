<?php

/*
 * Plugin Name: Flagship WooCommerce Shipping
 * Plugin URI: https://github.com/flagshipcompany/flagship-for-woocommerce
 * Description: An e-shipping courier solution that helps you shipping anything from Canada. Beautifully. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="http://smartship.flagshipcompany.com/">Sign up for an Smartship account</a> to get an API key, and 3) Go to <a href="/wp-admin/admin.php?page=wc-settings&tab=shipping&section=flagship_wc_shipping_method">settings</a> page to fill basic shipping credentials
 * Version: 0.1.6
 * Author: Flagship Courier Solution
 * Author URI: https://github.com/flagshipcompany/flagship-for-woocommerce
 * Requires at least: 4.4
 * Tested up to: 4.5.x
 *
 * Text Domain: flagship-for-woocommerce
 * Domain Path: /languages/
 *
 * Copyright: © 2016 Flagship Courier Solution.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// prevent data leak
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('FLAGSHIP_SHIPPING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FLAGSHIP_SHIPPING_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('FLAGSHIP_SHIPPING_PLUGIN_ID', 'flagship_shipping_method');
define('FLAGSHIP_NAME_PREFIX', 'flagship_');
define('FLAGSHIP_SHIPPING_TEXT_DOMAIN', 'flagship-for-woocommerce');

define('FLAGSHIP_SHIPPING_API_ENTRY_POINT', 'https://api.smartship.io');
define('FLAGSHIP_SHIPPING_API_TIMEOUT', 14);

require_once FLAGSHIP_SHIPPING_PLUGIN_DIR.'includes/update/class.flagship-autoupdate.php';

if (is_admin()) {
    $update = new Flagship_Autoupdate(__FILE__, 'flagshipcompany', 'flagship-for-woocommerce');
}

// Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    //
    require_once FLAGSHIP_SHIPPING_PLUGIN_DIR.'includes/class.flagship-application.php';

    Flagship_Application::init();
}

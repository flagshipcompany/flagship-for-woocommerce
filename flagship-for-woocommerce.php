<?php
/*
 * Plugin Name: FlagShip WooCommerce Shipping
 * Plugin URI: https://github.com/flagshipcompany/flagship-for-woocommerce
 * Description: An e-shipping courier solution that helps you shipping anything from Canada. Beautifully. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="http://smartship-ng.flagshipcompany.com/">Sign up for an FlagShip account</a> to get an API key, and 3) Go to settings page to fill basic shipping credentials
 * Version: 3.0.0
 * Author: FlagShip Courier Solutions
 * Requires at least: 4.6
 * Tested up to: 5.5.1
 * WC tested up to: 4.1.1
 *
 * Text Domain: flagship-for-woocommerce
 * Domain Path: /languages/
 *
 * Copyright: © 2017 FlagShip Courier Solution.
 * License: General Public License v3.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 */


defined( 'ABSPATH' ) || exit;

if (!defined( 'FLAGSHIP_PLUGIN_FILE' )) {
    define( 'FLAGSHIP_PLUGIN_FILE', __FILE__ );
}

if (file_exists(dirname( __FILE__ ) . '/env.php')) {
    include_once dirname( __FILE__ ) . '/env.php';
}

include_once dirname( __FILE__ ) . '/includes/UserFunctions.php';

if (!defined('FLAGSHIP_PLUGIN_NAME')){
    define("FLAGSHIP_PLUGIN_NAME", plugin_basename( __FILE__ ));
}

spl_autoload_register(function ($class) {
    $nameSpace = 'FlagshipWoocommerce\\';

    if (strncmp($nameSpace, $class, strlen($nameSpace)) === 0) {
        $relativeClass = substr($class, strlen($nameSpace));
        $filePath = str_replace('\\', '/', $relativeClass);
        include_once('includes/' . $filePath . '.php');
    }
});

$GLOBALS['flagship-woocommerce-shipping'] = FlagshipWoocommerce\FlagshipWoocommerceShipping::instance();

if (!class_exists('Flagship\\Shipping\\Flagship')) {
    include_once dirname(__FILE__). '/vendor/autoload.php';
}

if (dirname(dirname( __FILE__ )) == WPMU_PLUGIN_DIR) {
    load_muplugin_textdomain( 'flagship-for-woocommerce', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}  else {
    load_plugin_textdomain( 'flagship-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

if (defined( 'WP_CLI' ) && WP_CLI) {
    (new FlagshipWoocommerce\Commands\Console())->add_commands();
}

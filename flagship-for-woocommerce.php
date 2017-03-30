<?php

/*
 * Plugin Name: Flagship WooCommerce Shipping
 * Plugin URI: https://github.com/flagshipcompany/flagship-for-woocommerce
 * Description: An e-shipping courier solution that helps you shipping anything from Canada. Beautifully. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="http://smartship.flagshipcompany.com/">Sign up for an FlagShip account</a> to get an API key, and 3) Go to settings page to fill basic shipping credentials
 * Version: 1.1.4
 * Author: FlagShip Courier Solution
 * Author URI: https://github.com/flagshipcompany/flagship-for-woocommerce
 * Requires at least: 4.4
 * Tested up to: 4.7.3
 *
 * Text Domain: flagship-for-woocommerce
 * Domain Path: /languages/
 *
 * Copyright: © 2017 FlagShip Courier Solution.
 * License: MIT License
 * License URI: https://opensource.org/licenses/MIT
 */

// prevent data leak
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require __DIR__.'/src/Injection/I.php';

use FS\Injection\I;

I::boot(__DIR__);

define('FLAGSHIP_SHIPPING_PLUGIN_DIR', I::directory('PLUGIN'));
define('FLAGSHIP_SHIPPING_TEXT_DOMAIN', I::get('TEXT_DOMAIN'));

I::group(function () {
    \FS\Context\ApplicationContext::initialize(
        new \FS\Container(),
        new \FS\Configurations\WordPress\Configuration()
    );
}, [
    'dependencies' => ['woocommerce/woocommerce.php'],
]);

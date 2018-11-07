<?php

/*
 * Plugin Name: Flagship WooCommerce Shipping
 * Plugin URI: https://github.com/flagshipcompany/flagship-for-woocommerce
 * Description: An e-shipping courier solution that helps you shipping anything from Canada. Beautifully. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="http://smartship-ng.flagshipcompany.com/">Sign up for an FlagShip account</a> to get an API key, and 3) Go to settings page to fill basic shipping credentials
 * Version: 2.0.10
 * Author: FlagShip Courier Solution
 * Author URI: https://github.com/flagshipcompany/flagship-for-woocommerce
 * Requires at least: 4.6
 * Tested up to: 4.9.5
 * WC tested up to: 3.5.1
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

if (!class_exists('\\FS\\Injection\\I')) {
    require __DIR__.'/src/Injection/I.php';
}

use FS\Injection\I;
use FS\Context\ApplicationContext as App;
use FS\Container;
use FS\Configurator;

I::boot(__DIR__);

// convenient way to define text domain
define('FLAGSHIP_SHIPPING_TEXT_DOMAIN', I::textDomain());

// init app
I::group(function () {
    App::initialize(new Container(), new Configurator());
}, [
    'dependencies' => ['woocommerce/woocommerce.php'],
]);

\register_activation_hook(__FILE__, array('\\FS\\Injection\\I', 'fls_plugin_activate'));

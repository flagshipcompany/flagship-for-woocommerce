<?php

/*
 * Plugin Name: FlagShip WooCommerce Shipping
 * Plugin URI: https://github.com/flagshipcompany/flagship-for-woocommerce
 * Description: An e-shipping courier solution that helps you shipping anything from Canada. Beautifully. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="https://smartship-ng.flagshipcompany.com/">Sign up for an FlagShip account</a> to get an API key, and 3) Go to settings page to fill basic shipping credentials
 * Version: 3.0.29
 * Author: FlagShip Courier Solutions
 * Author URI: https://www.flagshipcompany.com
 * Requires at least: 4.6
 * Tested up to: 6.6.2
 * WC tested up to: 8.8.2
 *
 * Text Domain: flagship-for-woocommerce
 * Domain Path: /languages/
 *
 * Copyright: © 2023 FlagShip Courier Solutions.
 * License: General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
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

add_action('woocommerce_order_details_after_order_table', 'display_tracking_details', 10, 1);

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

function display_tracking_details($order)
{
    $trackingNumber = reset(get_post_meta($order->id, 'flagship_shipping_shipment_tracking_number'));
    $courierName = reset(get_post_meta($order->id, 'flagship_shipping_courier_name'));

    $url = "https://www.flagshipcompany.com/log-in/";
    if (strcasecmp($courierName, "purolator") == 0) {
        $url = 'https://eshiponline.purolator.com/ShipOnline/Public/Track/TrackingDetails.aspx?pup=Y&pin='.$trackingNumber.'&lang=E';
    }

    if (strcasecmp($courierName, "ups") == 0) {
        $url = 'http://wwwapps.ups.com/WebTracking/track?HTMLVersion=5.0&loc=en_CA&Requester=UPSHome&trackNums='.$trackingNumber.'&track.x=Track';
    }

    if (strcasecmp($courierName, "dhl") == 0) {
        $url = 'http://www.dhl.com/en/express/tracking.html?AWB='.$trackingNumber.'&brand=DHL';
    }

    if (strcasecmp($courierName, "fedex") == 0) {
        $url = 'http://www.fedex.com/Tracking?ascend_header=1&clienttype=dotcomreg&track=y&cntry_code=ca_english&language=english&tracknumbers='.$trackingNumber.'&action=1&language=null&cntry_code=ca_english';
    }

    if (strcasecmp($courierName, "canpar") == 0) {
        $url = 'https://www.canpar.com/en/track/TrackingAction.do?reference='.$trackingNumber.'&locale=en';
    }

    if (strcasecmp($courierName, "gls") == 0) {
        $url = "https://gls-group.com/CA/en/send-and-receive/track-a-shipment/?match=$trackingNumber";
    }

    if (strcasecmp($courierName, "nationex") == 0) {
        $url = 'https://www.nationex.com/en/track/tracking-report/?tracking[]='.$trackingNumber;
    }

    if (strcasecmp($courierName, "canadapost") == 0) {
        $url = 'https://www.canadapost-postescanada.ca/track-reperage/en#/details/'.$trackingNumber;
    }

    if (strlen($trackingNumber)) {
        echo '<p><a target="_blank" href="'.$url .'">Track Your Order Here</a></p>';
    }
}

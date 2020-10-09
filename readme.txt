=== FlagShip WooCommerce Extension  ===
Contributors: flagshipit
Tags: WooCommerce, shipping, ecommerce, e-commerce, courier, commerce
Requires at least: 4.6
Tested up to: 5.3
WC requires at least: 3.0.0
WC tested up to: 3.8.1
Stable tag: 1.0.0
Requires PHP: 7.1
License: General Public License v3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

FlagShip WooCommerce Extension obtains FlagShip shipping rates for orders and exports order to FlagShip to dispatch shipment.

== Description ==

    FlagShip WooCommerce Extension plugin adds a convenient shipping method to WordPress websites that run on Bedrock. Shipping rates from FlagShip will be displayed in the checkout of the online store. Orders can be exported to the FlagShip account of the business by one click.

== Installation ==

= system requirements =
- minimum:

Requires PHP v5.6+ WordPress v4.6+ WooCommerce v3.0+. For WooCommerce installation guide, please visit [woocommerce installation guide](https://docs.woothemes.com/document/installing-uninstalling-woocommerce/).

= non-technical requirements =
- Owning an activated **FlagShip Account** (If not, you can sign up for one at: https://www.flagshipcompany.com/sign-up/)
- Having a **FlagShip Shipping API** access token. ([click here](https://auth.smartship.io/tokens/) to request an access token!)

= Before starting, make sure you have properly done all of the following steps: =
- Owning a WordPress site with administrator access
- Having **WooCommerce** Plugin installed, activated, and configured

= In the folder of a Bedrock instance on the server, after WooCommerce is installed, run these two commands: =
- composer require wpackagist-plugin/flagship-for-woocommerce
- composer require flagshipcompany/flagship-api-sdk


= After installation =
- In the plugins page click "Activate" of the FlagShip WooCommerce Extension plugin
- Click "Settings" of the FlagShip WooCommerce Extension plugin
- In the settings, make sure the checkbox "Enable this shipping method" is checked
- Take some time to fill out all of the required or applicable settings. (Access Token is absolutely required. Otherwise, You won't be able to get discounted rate nor being able to print the shipping labels.)
- In the section `Box Split`, there are four options to choose from for the packing of items into package boxes. If you choose FlagShip packing, you need to provide boxes with dimensions.
- Then head to shipping zones to configure the settings for shipping. Go to WooCommerce Settings > Shipping > Shipping Zones and edit each shipping zone that needs the shipping settings to be configured. You can add and enable the FlagShip Shipping method. You also need to edit the FlagShip Shipping method of each shipping zone to save all the shipping settings for the shipping zone.


Once the FlagShip WooCommerce Extension plugin is installed, activate it and set up the shipping zones with the FlagShip shipping method, then FlagShip shipping rates will show up on the checkout page of the store.

== Frequently Asked Questions ==

Technical support will be provided at: developers@flagshipcompany.com, 1-866-320-8383

== Screenshots ==

== Upgrade Notice ==

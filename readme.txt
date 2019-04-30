=== FlagShip WooCommerce Shipping  ===
Contributors: flagshipit
Tags: WooCommerce, shipping, ecommerce, e-commerce, courier, commerce
Requires at least: 4.6
Tested up to: 5.1
WC tested up to: 3.6.2
Stable tag: 2.0.12
Requires PHP: 5.6
License: Apache License 2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0

FlagShip WooCommerce Shipping is an e-shipping courier solution that helps you shipping anything from Canada. Beautifully.

== Description ==

FlagShip WooCommerce Shipping plugin allows WooCommerce based stores to have the same convenient way to ship with FlagShip as on the FlagShip website. This plugin will display shipping rates on the shopping cart and checkout page. It also allows getting shipping rates in the wordpress admin site and creating a shipment. It can also enable the seller and the shopper to receive updates on a shipment. With a long list of parameters configurable, sellers can easily customize how shipping rates are displayed on store. Additionally, the order information can be exported to FlagShip website and all the shipping can be handled on the FlagShip website.

== Installation ==

= system requirements =
- minimum:

Requires PHP v5.3.2+ WordPress v4.4+ WooCommerce v2.5.5. For WooCommerce installation guide, please visit [woocommerce installation guide](https://docs.woothemes.com/document/installing-uninstalling-woocommerce/). 

- recommended:

Works better with PHP v5.6.x+ WordPress v4.6.x+ WooCommerce v2.6.x+. 

= non-technical requirements =
- Owning an activated **FlagShip Account** ([click here](https://smartship.flagshipcompany.com/company/register) to register for a new one)
- Having a **FlagShip Shipping API** access token. ([click here](https://auth.smartship.io/tokens/) to request an access token!)

= Before starting, make sure you have properly done all of the following steps: =
- Owning a WordPress site with administrator control
- Having **WooCommerce** Plugin _installed, activated, and configured_
- Able to access (FTP) WordPress installation's plugin directories (If you are unaware of doing so, check this [youtube video](https://www.youtube.com/watch?v=WULIQioBnOE))

= Installation Steps =
- Download flagship-for-woocommerce.zip zip archive;

Download Latest Release [Here](https://github.com/flagshipcompany/flagship-for-woocommerce/releases/latest), or browse all [historical releases](https://github.com/flagshipcompany/flagship-for-woocommerce/releases).

- Log in as admin on your WordPress website. Go to the dashboard. Then, go to plugins page (on left hand side navigation submenu).

![image](https://cloud.githubusercontent.com/assets/5373898/13267802/243b6414-da4d-11e5-9fc6-ed6ae38f0e06.png)

- Click on "Add New" and then "Upload plugin". Select the file you just downloaded (zip file) and upload it to the site.

- Click activate link under the title FlagShip WooCommerce Shipping

Ignore any notification or warning and click settings link under the title FlagShip WooCommerce Shipping

This is how the global settings of the plugin looks like:

![screen shot 2017-07-17 at 2 41 35 pm](https://user-images.githubusercontent.com/8826928/28284208-2cfeda16-6afe-11e7-9c01-871484d1e7ee.png)

![screen shot 2017-07-17 at 2 41 51 pm](https://user-images.githubusercontent.com/8826928/28284244-4c3f4c1c-6afe-11e7-85c1-e2f913d9b4e8.png)

- Enable flagShip shipping by checking the checbox `Enable this shipping method`
- Take some time to fill all of the settings. (Access Token is absolutely required. Otherwise, You won't be able to get discounted rate nor being able to print the shipping labels.)
- In the section `Parcel/Packaging`, there are four options to choose from for the packing of items into package boxes. If you choose FlagShip packing, you need to provide boxes with dimensions.

For WooCommerce v2.6.x or newer, you *must* additionally fill settings per each of shipping zones that had "FlagShip Shipping" as shipping method.

To configure the settings per shipping zone, go through WooCommerce Settings > Shipping > Shipping Zones and click on each of the link entitled by "FlagShip Shipping". You may customize your settings per shipping zones. i.e. different FlagShip Shipping settings for different shipping zones. 

As of v1.0.5, sooner as if FlagShip Shipping were added to the target shipping zone, all global settings will be copied into that target shipping zone.

**Lacking of shipping settings** per shipping zone may cause plugin to throw API warning such as

FlagShip API Error: Missing `x-smartship-token` header or could not find a valid token or your company is banned or disabled. Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.

This is how the shipping zones page might look like:

![capture all flagship shipping 100116](https://cloud.githubusercontent.com/assets/5373898/19041682/fa13c97e-8956-11e6-8907-df3f6728c1d1.png)

This is how it might look like your own shipping zone's settings:

![screen shot 2017-07-17 at 2 49 07 pm](https://user-images.githubusercontent.com/8826928/28284546-4fb67d6a-6aff-11e7-8281-737a4bb29ed9.png)

- Don't forget to save the settings by clicking on `Save changes` button on the bottom of the page.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

== Upgrade Notice ==
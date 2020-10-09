# flagship-for-woocommerce-bedrock

## Bedrock Installation

Instead of a standard WordPress install, you can install it with Bedrock by Roots. It is a boilerplate that uses the standard WordPress install and provides a better folder structure to accommodate different environments. Use the commands below

`composer create-project roots/bedrock`

Navigate to Wordpress Admin > Plugins > Search for WooCommerce and install it.

Navigate to the newly created folder on command line

`composer require flagshipcompany/flagship-for-woocommerce-bedrock:^1.0`

## Regular Wordpress Installation
Make sure WooCommerce is installed before installing the plugin.


Download zip file from GitHub.

Wordpress Admin > Plugins >  Add New > Upload plugin > Upload the plugin zip file.

Navigate to wp-content > plugins > flagship-for-woocommerce-bedrock on command line.

`composer install`

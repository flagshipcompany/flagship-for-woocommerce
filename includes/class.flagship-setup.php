<?php

/**
 * Set up Flagship WooCommerce plugin.
 */
class Flagship_Setup
{
    protected $flagship;
    protected $isAdmin = false;

    public function __construct(Flagship_Application $flagship)
    {
        $this->flagship = $flagship;
    }

    public function init($is_admin = false)
    {
        $actions = $this->flagship->actions;
        $filters = $this->flagship->filters;

        // add shipping method init.
        $actions->add('woocommerce_shipping_init');
        $filters->add('woocommerce_shipping_methods');

        if (!$is_admin) {
            return;
        }

        // add setting link to plugin page
        $filters->add('plugin_action_links_'.FLS__PLUGIN_BASENAME, 'plugin_page_setting_links_action');

        // when update a flagship setting value
        //add_action('woocommerce_settings_api_sanitized_fields_'.FLAGSHIP_SHIPPING_PLUGIN_ID, array($this->flagship, 'integrity'), 10, 1);

        //
        //add_action('woocommerce_update_options_shipping_'.FLAGSHIP_SHIPPING_PLUGIN_ID, array($this->flagship, 'settings_save'), 10, 3);

        // check for flagship app integrity
        // eg: token is set or not, etc.
        // if (!$this->flagship->is_installed()) {
        //     add_action('admin_notices', array($this->flagship, 'warning_installation'));
        // }

        //add_action('admin_notices', array($this->flagship, 'show_notifications'));
        // add meta boxes (eg: side box)
        $actions->add('add_meta_boxes');
        $actions->add('woocommerce_process_shop_order_meta');
    }
}

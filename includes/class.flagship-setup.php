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

        // add meta boxes (eg: side box)
        $actions->add('add_meta_boxes');
        $actions->add('woocommerce_process_shop_order_meta');
        // add pickup custom post type
        $actions->add('init', 'woocomerce_register_post_type_action');
    }
}

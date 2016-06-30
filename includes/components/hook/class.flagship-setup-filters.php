<?php

require_once __DIR__.'/class.flagship-api-hooks.php';

class Flagship_Setup_Filters extends Flagship_Api_Hooks
{
    protected $type = 'filter';

    public function bootstrap()
    {
        $this->add('woocommerce_shipping_methods');

        if (is_admin()) {
            // add setting link to plugin page
            $this->add('plugin_action_links_'.FLAGSHIP_SHIPPING_PLUGIN_BASENAME, 'plugin_page_setting_links_action');
        }
    }

    /**
     * Define shipping plugin name.
     *
     * @param array $methods list of shipping methods name
     *
     * @return array list of shipping methods name
     */
    public function woocommerce_shipping_methods_filter($methods)
    {
        $methods[] = 'Flagship_WC_Shipping_Method';

        return $methods;
    }

    /**
     * Create setting link in the plugin page.
     *
     * @param string $links
     * @param string $file
     *
     * @return string
     */
    public function plugin_page_setting_links_action($links, $file)
    {
        if ($file == FLAGSHIP_SHIPPING_PLUGIN_BASENAME) {
            array_unshift($links, $this->ctx['html']->a('flagship_shipping_settings', 'Settings', array(
                'escape' => true,
                'target' => true,
            )));
        }

        return $links;
    }
}

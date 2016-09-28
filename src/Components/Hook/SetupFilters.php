<?php

namespace FS\Components\Hook;

class SetupFilters extends Engine implements Factory\HookRegisterAwareInterface
{
    protected $type = 'filter';

    public function register()
    {
        $this->add('woocommerce_shipping_methods', 'registerShippingMethod');

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
    public function registerShippingMethod($methods)
    {
        $id = $this->getApplicationContext()->getComponent('\\FS\\Components\\Settings')['FLAGSHIP_SHIPPING_PLUGIN_ID'];

        if (\version_compare(WC()->version, '2.6', '>=')) {
            $methods[$id] = '\\FS\\Components\\Shipping\\Methods\\FlagshipWcShippingMethod';
        } else {
            $methods[] = '\\FS\\Components\\Shipping\\Methods\\LegacyFlagshipWcShippingMethod';
        }

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
            array_unshift($links, $this->getApplicationContext()->getComponent('\\FS\\Components\\Html')->a('flagship_shipping_settings', __('Settings', FLAGSHIP_SHIPPING_TEXT_DOMAIN), array(
                'escape' => true,
                'target' => true,
            )));
        }

        return $links;
    }
}

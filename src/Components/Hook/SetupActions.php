<?php

namespace FS\Components\Hook;

class SetupActions extends Engine implements Factory\HookRegisterAwareInterface
{
    protected $type = 'action';

    public function register()
    {
        $this->add('init');
    }

    /**
     * Enable translation.
     */
    public function init_action()
    {
        load_plugin_textdomain(FLAGSHIP_SHIPPING_TEXT_DOMAIN, false, 'flagship-for-woocommerce/languages/');
    }
}

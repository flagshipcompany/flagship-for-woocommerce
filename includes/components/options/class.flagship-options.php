<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Options extends Flagship_Component
{
    protected $options = array();

    public function bootstrap()
    {
        $this->sync();
    }

    public function get($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function equal($name, $value)
    {
        return $this->get($name) == $value;
    }

    public function not_equal($name, $value)
    {
        return $this->get($name) != $value;
    }

    public function sync()
    {
        $this->options = get_option('woocommerce_'.FLAGSHIP_SHIPPING_PLUGIN_ID.'_settings', array());

        return $this;
    }
}

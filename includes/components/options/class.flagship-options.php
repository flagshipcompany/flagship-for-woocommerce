<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Options extends Flagship_Component
{
    protected $options = array();
    protected $name;

    public function bootstrap()
    {
        $this->name = 'woocommerce_'.$this->ctx['configs']->get('FLAGSHIP_SHIPPING_PLUGIN_ID').'_settings';
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

    public function log($value)
    {
        $this->sync();

        $logs = $this->get('api_warning_log');
        $logs = $logs ? $logs : array();

        array_unshift($logs, array(
            'timestamp' => time(),
            'log' => $value,
        ));

        if (count($logs) > 10) {
            array_pop($logs);
        }

        return $this->set('api_warning_log', $logs);
    }

    public function set($key, $value)
    {
        $this->sync();
        $this->options[$key] = $value;

        update_option($this->name, $this->options);

        return $this;
    }

    public function sync()
    {
        $this->options = get_option($this->name, array());

        return $this;
    }

    public function all()
    {
        return $this->options;
    }
}

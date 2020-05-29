<?php

namespace FS\Components;

use FS\Injection\I;

class Options extends AbstractComponent implements Factory\ComponentPostConstructInterface
{
    protected $options = array();
    protected $wpOptionName;

    public function postConstruct()
    {
        $this->sync();
    }

    public function setWpOptionName($wpOptionName)
    {
        $this->wpOptionName = $wpOptionName;

        return $this;
    }

    public function get($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function eq($name, $value)
    {
        return $this->get($name) == $value;
    }

    public function neq($name, $value)
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

        update_option($this->wpOptionName, $this->options);

        return $this;
    }

    public function setTempValue($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function sync($instanceId = false)
    {
        if ($instanceId !== false) {
            $this->setWpOptionName('woocommerce_'.$this->getApplicationContext()->setting('FLAGSHIP_SHIPPING_PLUGIN_ID').'_'.$instanceId.'_settings');
        }

        $this->options = I::option($this->wpOptionName);

        //Use the general settings as backup, in case the option in the wp_options gets deleted.
        if ($instanceId !== false && !$this->options) {
            $this->setWpOptionName('woocommerce_'.$this->getApplicationContext()->setting('FLAGSHIP_SHIPPING_PLUGIN_ID').'_settings');
            $this->options = I::option($this->wpOptionName);
        }

        return $this;
    }

    public function all()
    {
        return $this->options;
    }
}

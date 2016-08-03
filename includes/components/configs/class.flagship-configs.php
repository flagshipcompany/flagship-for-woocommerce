<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Configs extends Flagship_Component
{
    protected $configs = array();

    public function get($name, $default = null)
    {
        return isset($this->configs[$name]) ? $this->configs[$name] : $default;
    }

    public function add($configs = array())
    {
        $this->configs = array_merge($this->configs, $configs);

        return $this;
    }
}

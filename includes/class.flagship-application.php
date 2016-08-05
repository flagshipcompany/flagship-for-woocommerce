<?php

class Flagship_Application implements ArrayAccess
{
    public static $_instance;

    protected $container;

    public function load($name, $force_load = false)
    {
        if (isset($this[strtolower($name)]) && !$force_load) {
            return $this;
        }

        $provider = $this->factory($name);
        $provider->provide($this);

        return $this;
    }

    public function dependency(array $dependencies)
    {
        foreach ($dependencies as $dependency) {
            if (!isset($this[$dependency])) {
                $this->load($dependency);
            }
        }

        return $this;
    }

    public function provider($name)
    {
        return isset($this[$name]) ? $this[$name] : null;
    }

    // array access methods
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function factory($className)
    {
        $class = str_replace('_', '-', strtolower($className));

        $filePath = FLAGSHIP_SHIPPING_PLUGIN_DIR.'includes/components/'.$class.'/class.flagship-'.$class.'.provider.php';
        $realClassName = 'Flagship_'.$className.'_Provider';

        if (!file_exists($filePath) || !$realClassName) {
            throw new Exception($className.' does not exist.');
        }

        require_once $filePath;

        return new $realClassName();
    }

    public static function init($configs = array())
    {
        $ctx = self::get_instance();

        $ctx->load('Configs');
        $ctx['configs']->add($configs);
        if ($ctx['configs']->get('FLAGSHIP_SHIPPING_PLUGIN_DEBUG')) {
            $ctx['configs']->add(array(
                'FLAGSHIP_SHIPPING_API_ENTRY_POINT' => 'http://127.0.0.1:3002',
            ));

            $ctx->load('Console');
        }

        $ctx->dependency(array(
            'Request',
            'Html',
            'View',
            'Options',
            'Client',
            'Notification',
            'Validation',
            'Hook',
            'Url',
            'Address',
        ));

        return $ctx;
    }

    public static function get_instance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

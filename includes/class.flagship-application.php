<?php

require_once FLS__PLUGIN_DIR.'includes/hook/class.flagship-api-hooks.php';

require_once FLS__PLUGIN_DIR.'includes/class.flagship-view.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-html.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-request-formatter.php';

class Flagship_Application implements ArrayAccess
{
    public static $_instance;
    public $text_domain;
    public $actions;
    public $filters;

    protected $container;

    public function __construct()
    {
        // register providers
        $this->register('Options');
        $this->register('Client');
        $this->register('Notification');
        $this->register('Validation');
        $this->register('Hook');
        $this->register('Url');
        $this->register('Address');

        $this->text_domain = 'flagship_shipping';
    }

    public function register($name)
    {
        $provider = self::factory($name, 'component');
        $provider->provide($this);

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

    // static methods
    //
    public static function factory($className, $type = 'flagship')
    {
        $class = str_replace('_', '-', strtolower($className));

        $filePath = FLS__PLUGIN_DIR.'includes/';
        $realClassName = '';

        switch ($type) {
            case 'flagship':
                $filePath .= 'class.flagship-'.$class.'.php';
                $realClassName = 'Flagship_'.$className;
                break;
            case 'hook':
                $filePath .= 'hook/class.flagship-'.$class.'.php';
                $realClassName = 'Flagship_'.$className;
                break;
            case 'component':
                $filePath .= 'components/'.$class.'/class.flagship-'.$class.'.provider.php';
                $realClassName = 'Flagship_'.$className.'_Provider';
        }

        if (!file_exists($filePath) || !$realClassName) {
            throw new Exception($className.' does not exist.');
        }

        require_once $filePath;

        return new $realClassName();
    }

    public static function init($is_admin = false)
    {
        $flagship = self::get_instance();

        $setup = self::factory('Setup');

        $setup->set_application($flagship);
        $setup->init($is_admin);

        return $flagship;
    }

    public static function get_instance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Api_Hooks extends Flagship_Component
{
    // filter only
    protected $hits = array();

    public function add($filter_name, $optional_method_name = false)
    {
        return $this->add_hook($this->type, $filter_name, $optional_method_name);
    }

    public function remove($filter_name, $method = null)
    {
        return $this->remove_hook($this->type, $filter_name, $method);
    }

    public function has($filter_name, $optional_method_name = false)
    {
        return $this->has_hook($this->type, $filter_name, $optional_method_name);
    }

    public function on($filter_name, $args = array())
    {
        if ($this->type == 'action') {
            return $this->on_hook($this->type, $filter_name, $args);
        }

        if (isset($this->hits[$filter_name])) {
            return $this->hits[$filter_name];
        }

        $this->hits[$filter_name] = $this->on_hook($this->type, $filter_name, $args);

        return $this->hits[$filter_name];
    }

    public function one($filter_name, $args = array())
    {
        return $this->one_hook($this->type, $filter_name, $args);
    }

    public function __get($name)
    {
        if ($name === 'external') {
            return $this;
        }

        return;
    }

    protected function add_hook($hook_type, $hook_name, $optional_method_name = false)
    {
        $hook = 'add_'.$hook_type;
        $method = $this->get_optional_method($optional_method_name);

        // apply default naming convention
        if (!$method) {
            $method = array($this, $hook_name.'_'.$hook_type);
        }

        if (is_array($method)) {
            $rf = new ReflectionMethod($method[0], $method[1]);

            return $hook($hook_name, $method, 10, $rf->getNumberOfParameters());
        }

        $rf = new ReflectionFunction($method);

        return $hook($hook_name, $method, 10, $rf->getNumberOfParameters());
    }

    protected function remove_hook($hook_type, $hook_name, $method_name = null)
    {
        if (!$this->hook_type_exist($hook_type)) {
            return false;
        }

        $hook = 'remove_'.$hook_type;
        $method = $this->get_optional_method($method_name);

        if (!$method) {
            $method = array($this, $hook_name.'_'.$hook_type);
        }

        return $hook($hook_name, $method);
    }

    protected function has_hook($hook_type, $hook_name, $optional_method_name = false)
    {
        if (!$this->hook_type_exist($hook_type)) {
            return false;
        }

        $hook = 'has_'.$hook_type;
        $optional_method = $this->get_optional_method($optional_method_name);

        if (!$optional_method) {
            return $hook($hook_name);
        }

        return $hook($hook_name, $optional_method);
    }

    public function on_hook($hook_type, $hook_name, $data)
    {
        if ($hook_type == 'filter') {
            $hook = 'apply_'.$hook_type.'s_ref_array';
        } else {
            $hook = 'do_'.$hook_type.'_ref_array';
        }

        return $hook($hook_name, $data);
    }

    protected function one_hook($hook_type, $hook_name, $data)
    {
        $ret = $this->on_hook($hook_type, $hook_name, $data);

        $this->remove_hook($hook_type, $hook_name);

        return $ret;
    }

    protected function hook_type_exist($hook_type)
    {
        return $hook_type == 'filter' || $hook_type == 'action';
    }

    protected function get_optional_method($optional_method_name)
    {
        if (!$optional_method_name) {
            return false;
        }

        // support method decalration in different class (not in a subclass of this one)
        if (is_array($optional_method_name) && method_exists($optional_method_name[0], $optional_method_name[1])) {
            return $optional_method_name;
        }

        if (function_exists($optional_method_name)) {
            return $optional_method_name;
        }

        if (method_exists($this, $optional_method_name)) {
            return array($this, $optional_method_name);
        }

        return false;
    }
}

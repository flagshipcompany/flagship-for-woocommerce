<?php

class Flagship_Api_Hooks
{
    public function add_hook($hook_type, $hook_name, $optional_method_name = false)
    {
        $hook = 'add_'.$hook_type;
        $method = $this->get_optional_method($optional_method_name);

        // apply default naming convention
        if (!$method) {
            $method = array(get_class($this), $hook_name.'_'.$hook_type);
        }

        if (is_array($method)) {
            $rf = new ReflectionMethod($method[0], $method[1]);

            return $hook($hook_name, $method, 10, $rf->getNumberOfParameters());
        }

        $rf = new ReflectionFunction($method);

        return $hook($hook_name, $method, 10, $rf->getNumberOfParameters());
    }

    public function remove_hook($hook_type, $hook_name, $method_name)
    {
        if (!$this->hook_type_exist($hook_type)) {
            return false;
        }

        $hook = 'remove_'.$hook_type;
        $method = $this->get_optional_method($method_name);

        if (!$method) {
            $method = array(get_class($this), $hook_name.'_'.$hook_type);
        }

        return $hook($hook_name, $method);
    }

    public function has_hook($hook_type, $hook_name, $optional_method_name = false)
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

    protected function hook_type_exist($hook_type)
    {
        return $hook_type == 'filter' || $hook_type == 'action';
    }

    protected function get_optional_method($optional_method_name)
    {
        if (!$optional_method_name) {
            return false;
        }

        if (function_exists($optional_method_name)) {
            return $optional_method_name;
        }

        $class = get_class($this);

        if (method_exists($class, $optional_method_name)) {
            return array($class, $optional_method_name);
        }

        return false;
    }
}

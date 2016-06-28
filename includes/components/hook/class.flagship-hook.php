<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_Hook extends Flagship_Component
{
    protected $path = __DIR__;
    protected $container = array();

    /**
     * Load a hook (action/filter) if necessary.
     *
     * @param string $signature  name to reference loaded hook
     * @param string $class_name class name of hook
     */
    public function load($signature, $class_name)
    {
        if ($hook = $this->get($signature)) {
            return $hook;
        }

        require_once $this->path.'/class.flagship-'.strtolower(str_replace('_', '-', $class_name)).'.php';

        $class = 'Flagship_'.$class_name;

        $this->container[$signature] = new $class($this->ctx);
    }

    /**
     * Get a hook object.
     *
     * @param string $signature name to reference loaded hook
     *
     * @return object
     */
    public function get($signature)
    {
        return $this->container[$signature];
    }
}

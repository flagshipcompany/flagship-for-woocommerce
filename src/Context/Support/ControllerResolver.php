<?php

namespace FS\Context\Support;

class ControllerResolver
{
    protected $controller;
    protected $context;
    protected $mapping;
    protected $afterCb = null;

    public function __construct($controller, $context, $mapping)
    {
        $this->controller = $controller;
        $this->context = $context;
        $this->mapping = $mapping;
    }

    public function before(callable $cb)
    {
        $cb($this->context);

        return $this;
    }

    public function dispatch($action, array $payload = [])
    {
        if (!isset($this->mapping[$action])) {
            return;
        }

        $ret = call_user_func_array([$this->controller, $this->mapping[$action]], array_merge([$this->context->_('\\FS\\Components\\Web\\RequestParam'), $this->context], $payload));

        if ($this->afterCb) {
            $cb = $this->afterCb;

            $cb($this->context);
        }

        return $ret;
    }

    public function after(callable $cb)
    {
        $this->afterCb = $cb;

        return $this;
    }
}

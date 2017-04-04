<?php

namespace FS\Context\Support;

class ControllerResolver
{
    protected $controller;
    protected $context;
    protected $afterCb = null;

    public function __construct($controller, $context)
    {
        $this->controller = $controller;
        $this->context = $context;
    }

    public function before(callable $cb)
    {
        $cb($this->context);

        return $this;
    }

    public function dispatch($action, array $payload = [])
    {
        $ret = call_user_func_array([$this->controller, $action], array_merge([$this->context->_('\\FS\\Components\\Web\\RequestParam'), $this->context], $payload));

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

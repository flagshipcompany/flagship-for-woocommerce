<?php

namespace FS\Context\Support;

abstract class AbstractApplicationObjectSupport implements \FS\Context\ApplicationContextAwareInterface
{
    protected $ctx = null;

    public function getApplicationContext()
    {
        if (!$this->ctx && $this->isContextRequired()) {
            throw new \Exception('ApplicationObjectSupport instance ['.spl_object_hash($this).'] does not run in an ApplicationContext');
        }

        return $this->ctx;
    }

    final public function setApplicationContext(\FS\Context\ApplicationContextInterface $ctx = null)
    {
        if (!$this->ctx && !$this->isContextRequired()) {
            return $this;
        }

        $reflected = new \ReflectionClass($this->requiredContextClass());

        if (!$this->ctx && !$reflected->isInstance($ctx)) {
            throw new \Exception('Invalid application context: needs to be of type ['.$this->requiredContextClass().']');
        }

        $this->ctx = $ctx;

        return $this;
    }

    protected function isContextRequired()
    {
        return false;
    }

    protected function requiredContextClass()
    {
        return '\\FS\\Context\\ApplicationContextInterface';
    }
}

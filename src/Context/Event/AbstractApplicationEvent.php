<?php

namespace FS\Context\Event;

use FS\Context\ApplicationEventInterface;

abstract class AbstractApplicationEvent implements ApplicationEventInterface
{
    protected $inputs = null;

    public function setInputs($inputs)
    {
        $this->inputs = $inputs;

        return $this;
    }

    public function getInputs()
    {
        return $this->inputs;
    }

    public function getInput($key, $default = null)
    {
        if (isset($this->inputs[$key])) {
            return $this->inputs[$key];
        }

        return $default;
    }
}

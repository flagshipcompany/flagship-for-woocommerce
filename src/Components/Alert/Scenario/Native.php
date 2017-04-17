<?php

namespace FS\Components\Alert\Scenario;

use FS\Components\AbstractComponent;

class Native extends AbstractComponent
{
    protected $store = [];

    public function isEmpty()
    {
        return (bool) $store;
    }

    public function add($type, $message)
    {
        if (!isset($this->store[$type])) {
            $this->store[$type] = [];
        }

        while (is_array($message) && $message) {
            $msg = array_shift($message);

            $this->add($type, $msg);
        }

        if (is_string($message)) {
            $hash = md5($message);

            $this->store[$type][$hash] = $message;
        }

        return $this;
    }

    public function reverseOrdering($type)
    {
        if (isset($this->store[$type])) {
            $this->store[$type] = array_reverse($this->store[$type]);
        }

        return $this;
    }

    public function view($viewer)
    {
        $viewer->notification(['notifications' => $this->store]);

        return $this;
    }
}

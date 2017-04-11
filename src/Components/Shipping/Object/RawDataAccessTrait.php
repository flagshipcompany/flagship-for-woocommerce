<?php

namespace FS\Components\Shipping\Object;

trait RawDataAccessTrait
{
    protected $raw = [];

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->raw = array_merge($this->raw, $$key);

            return $this;
        }

        $this->raw[$key] = $value;

        return $this;
    }

    public function get($key)
    {
        return $this->raw[$key];
    }

    public function remove($key)
    {
        unset($this->raw[$key]);

        return $this;
    }
}

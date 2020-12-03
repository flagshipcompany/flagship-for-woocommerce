<?php

namespace FS\Components\Model;

use FS\Components\AbstractComponent;

abstract class AbstractModel extends AbstractComponent implements \ArrayAccess, \JsonSerializable
{
    protected $container = array();

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

    public function jsonSerialize()
    {
        return $this->container;
    }
}

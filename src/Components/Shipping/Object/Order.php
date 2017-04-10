<?php

namespace FS\Components\Shipping\Object;

class Order
{
    protected $nativeOrder;

    public function getId()
    {
        return $this->native('id');
    }

    public function getAttribute($attribute)
    {
        return \get_post_meta($this->getId(), $attribute, true);
    }

    public function setAttribute($attribute, $value)
    {
    }

    public function setNativeOrder($nativeOrder)
    {
        $this->nativeOrder = $nativeOrder;

        return $this;
    }

    public function native($key = null)
    {
        if (!$key) {
            return $this->nativeOrder;
        }

        return $this->nativeOrder->{$key};
    }
}

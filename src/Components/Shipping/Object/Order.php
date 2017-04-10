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
        \update_post_meta($this->getId(), $attribute, $value);

        return $this;
    }

    public function removeAttribute($attribute)
    {
        \delete_post_meta($this->getId(), $attribute);

        return $this;
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

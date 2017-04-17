<?php

namespace FS\Components\Shipping\Object;

class Order
{
    protected $nativeOrder;

    public function getId()
    {
        return $this->native('id');
    }

    public function hasAttribute($attribute)
    {
        return $this->nativeOrder->meta_exists($attribute);
    }

    public function getAttribute($attribute, $single = true)
    {
        return $this->nativeOrder->get_meta($attribute, $single);
    }

    public function setAttribute($attribute, $value)
    {
        $this->nativeOrder->update_meta_data($attribute, $value);

        // this is important, the CRUD update method will only change object's value
        $this->nativeOrder->save();

        return $this;
    }

    public function removeAttribute($attribute)
    {
        $this->nativeOrder->delete_meta_data($attribute);

        // this is important, the CRUD delete method will only change object's value
        $this->nativeOrder->save();

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

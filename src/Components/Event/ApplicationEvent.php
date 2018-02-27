<?php

namespace FS\Components\Event;

use FS\Context\Event\AbstractApplicationEvent;

class ApplicationEvent extends AbstractApplicationEvent
{
    const CALCULATE_SHIPPING = 1;
    const METABOX_DISPLAY = 2;
    const METABOX_OPERATIONS = 3;
    const PICKUP_POST_TYPE = 4;
    const PLUGIN_PAGE_SETTING_LINK = 5;
    const SHIPPING_METHOD_SETUP = 6;
    const SHIPPING_ZONE_METHOD_ADD = 7;
    const SHIPPING_ZONE_METHOD_OPTIONS = 8;
    const PLUGIN_INITIALIZATION = 9;
    const CART_FAKE_SHIPPING_RATE_DISCOUNT = 10;
    const SIGNATURE_REQUIRED_IN_CHECKOUT = 11;
    const UPDATE_SIGNATURE_IN_ORDER = 12;

    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }
}

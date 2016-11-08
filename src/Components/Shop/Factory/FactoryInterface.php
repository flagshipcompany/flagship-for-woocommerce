<?php

namespace FS\Components\Shop\Factory;

interface FactoryInterface
{
    const RESOURCE_ORDER = 'order';
    const RESOURCE_ORDER_COLLECTION = 'orders';
    const RESOURCE_SHIPMENT = 'shipment';

    public function getModel($resource, $context = array());
}

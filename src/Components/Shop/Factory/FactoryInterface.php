<?php

namespace FS\Components\Shop\Factory;

interface FactoryInterface
{
    const RESOURCE_ORDER = 'order';
    const RESOURCE_SHIPMENT = 'shipment';

    public function getOrder($resource, $context = array());
}

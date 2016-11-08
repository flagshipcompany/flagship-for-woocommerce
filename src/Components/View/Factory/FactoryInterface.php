<?php

namespace FS\Components\View\Factory;

interface FactoryInterface
{
    public function getView($resource, $context = array());
}

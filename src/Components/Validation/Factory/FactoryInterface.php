<?php

namespace FS\Components\Validation\Factory;

interface FactoryInterface
{
    public function getValidator($resource, $context = array());
}

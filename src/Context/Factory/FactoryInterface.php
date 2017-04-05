<?php

namespace FS\Context\Factory;

interface FactoryInterface
{
    public function resolve($target, $option = []);
}

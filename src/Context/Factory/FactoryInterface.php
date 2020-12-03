<?php

namespace FS\Context\Factory;

interface FactoryInterface
{
    public function resolve($target, array $option = []);

    public function resolveWithoutContext($target, array $option = []);
}

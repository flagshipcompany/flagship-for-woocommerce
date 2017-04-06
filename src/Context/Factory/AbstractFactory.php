<?php

namespace FS\Context\Factory;

use FS\Components\AbstractComponent;

abstract class AbstractFactory extends AbstractComponent implements FactoryInterface
{
    public function resolve($resource, array $option = [])
    {
        $resolved = $this->resolveWithoutContext($resource, $option);

        if ($resolved) {
            return $resolved->setApplicationContext($this->getApplicationContext());
        }

        throw new \Exception('Unable to resolve: '.$resource, 500);
    }
}

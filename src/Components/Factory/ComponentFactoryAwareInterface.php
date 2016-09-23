<?php

namespace FS\Components\Factory;

interface ComponentFactoryAwareInterface
{
    public function setComponentFactory(ComponentFactoryInterface $factory);
}

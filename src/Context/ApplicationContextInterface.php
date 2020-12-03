<?php

namespace FS\Context;

interface ApplicationContextInterface
{
    public function setContainer(\FS\Container $container);

    public function getContainer();

    public function setConfiguration(\FS\Components\Factory\ConfigurationInterface $configuration);

    public function getConfiguration();
}

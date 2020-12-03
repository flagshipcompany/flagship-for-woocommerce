<?php

namespace FS\Context;

use FS\Components\Factory\ConfigurationInterface;

interface ConfigurableApplicationContextInterface extends ApplicationContextInterface
{
    public function addApplicationListener(ApplicationListenerInterface $listener);

    public function configure(ConfigurationInterface $configurator);
}

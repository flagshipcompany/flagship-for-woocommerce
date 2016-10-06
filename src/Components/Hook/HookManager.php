<?php

namespace FS\Components\Hook;

class HookManager extends \FS\Components\AbstractComponent implements \FS\Components\Factory\ComponentInitializingInterface
{
    public function afterPropertiesSet()
    {
        $this->registerHook('\\FS\\Components\\Hook\\SetupActions');
        // $this->registerHook('\\FS\\Components\\Hook\\SetupFilters');
        // $this->registerHook('\\FS\\Components\\Hook\\MetaboxActions');
        $this->registerHook('\\FS\\Components\\Hook\\PickupPostTypeActions');
    }

    public function registerHook($class)
    {
        $this->getApplicationContext()->getComponent($class)->register();
    }
}

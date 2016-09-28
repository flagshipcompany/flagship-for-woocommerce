<?php

namespace FS\Components\Hook;

class HookManager extends \FS\Components\AbstractComponent implements \FS\Components\Factory\ComponentInitializingInterface
{
    protected $hookCtx;

    public function afterPropertiesSet()
    {
        $this->hookCtx = new \FS\Components\Hook\Context\HookContext();

        $this->hookCtx->setContainer(new \FS\Container());
        $this->hookCtx->setConfiguration(new \FS\Components\Hook\Configuration());

        $this->registerHook('\\FS\\Components\\Hook\\SetupActions');
        $this->registerHook('\\FS\\Components\\Hook\\SetupFilters');
        $this->registerHook('\\FS\\Components\\Hook\\MetaBoxActions');
        $this->registerHook('\\FS\\Components\\Hook\\PickupPostTypeActions');
    }

    public function registerHook($class)
    {
        $this->hookCtx->getComponent($class)->setApplicationContext($this->getApplicationContext())->register();
    }
}

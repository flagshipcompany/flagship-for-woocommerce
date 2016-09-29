<?php

namespace FS\Components\Hook;

class Configuration implements \FS\Components\Factory\ConfigurationInterface
{
    public function getSetupActions()
    {
        return new SetupActions();
    }

    public function getSetupFilters()
    {
        return new SetupFilters();
    }

    public function getSettingsFilters()
    {
        return new SettingsFilters();
    }

    public function getMetaBoxActions()
    {
        return new MetaBoxActions();
    }
}

<?php

require_once FLS__PLUGIN_DIR.'includes/hook/class.flagship-hook.php';

class Flagship_Hook_Provider
{
    public function provide(Flagship_Application $flagship)
    {
        $flagship['hooks'] = new Flagship_Hook();
    }
}

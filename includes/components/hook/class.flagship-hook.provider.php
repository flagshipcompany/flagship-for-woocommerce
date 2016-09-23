<?php

require_once __DIR__.'/class.flagship-hook.php';

class Flagship_Hook_Provider
{
    public function provide(FSApplicationContext $ctx)
    {
        $ctx['hooks'] = new Flagship_Hook($ctx);

        $ctx['hooks']->load('setup.filters', 'Setup_Filters');
        $ctx['hooks']->load('setup.actions', 'Setup_Actions');

        $ctx['hooks']->load('metabox.actions', 'Metabox_Actions');
    }
}

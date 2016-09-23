<?php

namespace FS\Components\Hook;

class PickupPostTypeActions extends Engine implements Factory\HookRegisterAwareInterface
{
    protected $type = 'action';

    public function register()
    {
        if (!is_admin()) {
            return;
        }

        // add pickup custom post type
        $this->add('init');
    }

    public function init_action()
    {
    }
}

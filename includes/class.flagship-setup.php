<?php

/**
 * Set up Flagship WooCommerce plugin.
 */
class Flagship_Setup
{
    protected $flagship;
    protected $isAdmin = false;

    public function __construct(Flagship_Application $flagship)
    {
        $this->flagship = $flagship;
    }

    public function init($is_admin = false)
    {
        $this->flagship->hooks->load('setup.filters', 'Setup_Filters');
        $this->flagship->hooks->load('setup.actions', 'Setup_Actions');

        $this->flagship->hooks->load('metabox.actions', 'Metabox_Actions');
    }
}

<?php
namespace FlagshipWoocommerce\Commands;

class Console {

	protected $namespace = 'fcs';

    public function add_commands() 
    {
        \WP_CLI::add_command("{$this->namespace} settings", (new Settings_Command()));
        \WP_CLI::add_command("{$this->namespace} zones", (new Zones_Command()));
        \WP_CLI::add_command("{$this->namespace} orders", (new Orders_Command()));
    }
}
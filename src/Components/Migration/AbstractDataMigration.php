<?php

namespace FS\Components\Migration;

use FS\Injection\I;

abstract class AbstractDataMigration
{
    protected $flagship_shipping_plugin_id;

    protected $migration_id;

    protected $flagship_migrations_option_key;

    abstract protected function update_fls_plugin_settings($option_key);

    public function __construct($flagship_shipping_plugin_id)
    {
        $this->flagship_shipping_plugin_id = $flagship_shipping_plugin_id;
        $this->flagship_migrations_option_key = $this->flagship_shipping_plugin_id.'_data_migrations';
    }

    public function migrate()
    {
        if ($this->find_flagship_migration($this->migration_id)) {
            return;
        }

        $general_settings_option_key = 'woocommerce_'.$this->flagship_shipping_plugin_id.'_settings';
        $option_keys = I::get_all_instance_option_keys();
        $option_keys[] = $general_settings_option_key;
        $migration_results = array();

        foreach ($option_keys as $key => $value) {
            $migration_results[] = $this->update_fls_plugin_settings($value);
        }

        if (!in_array(false, $migration_results)) {
            $flagship_migrations = \get_option($this->flagship_migrations_option_key);
            $flagship_migrations = $flagship_migrations ? $flagship_migrations : [];
            $flagship_migrations[] = $this->migration_id;
            $optionResult = \update_option($this->flagship_migrations_option_key, $flagship_migrations);
        }
    }

    protected function find_flagship_migration()
    {
        $flagship_migrations = \get_option($this->flagship_migrations_option_key);

        return $flagship_migrations && in_array($this->migration_id, $flagship_migrations);
    }
}

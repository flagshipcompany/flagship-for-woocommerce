<?php

namespace FS\Components\Migration;

class BoxSplitDataMigration extends AbstractDataMigration
{
    protected $migration_id = 'box_split_data_migration';

    protected function update_fls_plugin_settings($option_key)
    {
        $settings = \get_option($option_key);

        if (!$settings || !isset($settings['enable_packing_api'])) {
            return true;
        }

        if ('yes' == $settings['enable_packing_api'] && isset($settings['default_package_box_split'])) {
            $settings['default_package_box_split'] = 'packing';
        }

        unset($settings['enable_packing_api']);

        return \update_option($option_key, $settings);
    }
}

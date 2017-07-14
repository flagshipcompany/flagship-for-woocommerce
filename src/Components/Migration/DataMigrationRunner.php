<?php

namespace FS\Components\Migration;

class DataMigrationRunner
{
    protected $flagship_shipping_plugin_id;

    public function __construct($flagship_shipping_plugin_id)
    {
        $this->flagship_shipping_plugin_id = $flagship_shipping_plugin_id;
    }

    public function run()
    {
        (new BoxSplitDataMigration($this->flagship_shipping_plugin_id))->migrate();
    }
}

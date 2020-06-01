<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

return [
    'version' => '2.0.17',
    'autoload' => [
        'psr-4' => [
            'FS\\' => 'src/',
            'FS\\Test\\' => 'tests/',
        ],
    ],
    'extra' => [
        'BASENAME' => plugin_basename(__DIR__.'/flagship-for-woocommerce.php'),
        'DIRECTORY' => [
            'ASSETS' => '/assets',
            'PUBLIC_URL' => plugin_dir_url(__FILE__),
            'PLUGIN' => plugin_dir_path(__FILE__),
            'VIEWS' => '/templates',
        ],
    ],
    'debug' => false,
    'auto-updater' => true,
    'text-domain' => 'flagship-for-woocommerce',
    'bootstrap' => 'flagship-for-woocommerce.php',
];

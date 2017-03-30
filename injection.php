<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

return [
    'version' => '1.1.4',
    'autoload' => [
        'psr-4' => [
            'FS\\' => 'src/',
            'FS\\Test\\' => 'tests/',
        ],
    ],
    'extra' => [
        'BASENAME' => plugin_basename(__DIR__.'/flagship-for-woocommerce.php'),
        'GRANT_ACCESS_VERIFICATION_ENDPOINT' => 'https://test-smartshipng.flagshipcompany.com/webhook/grant-access',
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

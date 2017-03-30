<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

return [
    'autoload' => [
        'psr-4' => [
            'FS\\' => 'src/',
            'FS\\Injection\\' => 'src/Injection/',
            'FS\\Test\\' => 'tests/',
        ],
    ],
    'extra' => [
        'GRANT_ACCESS_VERIFICATION_ENDPOINT' => 'https://test-smartshipng.flagshipcompany.com/webhook/grant-access',
        'TEXT_DOMAIN' => 'flagship_secure',
        'ASSETS_DIR' => '/assets',
        'PUBLIC_URL_DIR' => plugin_dir_url(__FILE__),
        'PLUGIN_DIR' => plugin_dir_path(__FILE__),
        'VIEWS_DIR' => '/templates',
    ],
    'debug' => false,
];

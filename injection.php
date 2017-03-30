<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

return [
    'version' => '1.1.5',
    'autoload' => [
        'psr-4' => [
            'FS\\' => 'src/',
            'FS\\Injection\\' => 'src/Injection/',
            'FS\\Test\\' => 'tests/',
        ],
    ],
    'extra' => [
        'BASENAME' => plugin_basename(__DIR__.'/flagship-for-woocommerce.php'),
        'GRANT_ACCESS_VERIFICATION_ENDPOINT' => 'https://test-smartshipng.flagshipcompany.com/webhook/grant-access',
        'TEXT_DOMAIN' => 'flagship-for-woocommerce',
        'DIRECTORY' => [
            'ASSETS' => '/assets',
            'PUBLIC_URL' => plugin_dir_url(__FILE__),
            'PLUGIN' => plugin_dir_path(__FILE__),
            'VIEWS' => '/templates',
        ],
    ],
    'debug' => false,
];

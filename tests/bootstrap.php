<?php

// require __DIR__.'/../src/Injection/I.php';

require_once dirname(__DIR__).'/vendor/autoload.php';

\FS\Test\Helper\Bootstrap::initialize(
    dirname(__FILE__),
    getenv('WP_TESTS_DIR') ? getenv('WP_TESTS_DIR') : '/tmp/wordpress-tests-lib'
);

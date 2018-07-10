<?php

if (!@include(__DIR__ . '/../vendor/autoload.php')) {
    die("You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install --dev
");
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/index.php';
require_once __DIR__ . '/includes/BaseWPMockTestCase.php';

<?php

$autoload = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoload)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}

$loader = require($autoload);

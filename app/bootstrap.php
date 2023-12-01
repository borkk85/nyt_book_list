<?php


require_once dirname(__DIR__) . '../vendor/autoload.php';

// Assuming .env is in the project root and bootstrap.php is in a subdirectory
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Include other bootstrap code like custom autoloaders if needed

// Load config
require_once '../app/config/config.php';
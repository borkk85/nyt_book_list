<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Load config
require_once dirname(__DIR__) . '/app/config/config.php';
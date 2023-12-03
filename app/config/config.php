<?php

// DB PARAMS

// Using getenv() function to fetch data from .env file
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);

// URL Root and Site Name
define('URLROOT', $_ENV['URLROOT']);
define('SITENAME', $_ENV['SITENAME']);

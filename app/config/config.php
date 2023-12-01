<?php

// DB PARAMS

// Using getenv() function to fetch data from .env file
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_NAME', getenv('DB_NAME'));

// URL Root and Site Name
define('URLROOT', getenv('URLROOT'));
define('SITENAME', getenv('SITENAME'));
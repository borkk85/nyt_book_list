<?php

// Load config

require_once 'config/config.php';

// Autoload Core Libraries and Services

spl_autoload_register(function($className){
    
    $libraries_path = APPROOT . '/libraries/' . $className . '.php';
    $services_path = APPROOT . '/services/' . $className . '.php';

    if (file_exists($libraries_path)) {
        require_once $libraries_path;
    } elseif (file_exists($services_path)) {
        require_once $services_path;
    }
    
});
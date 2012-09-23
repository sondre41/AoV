<?php

/**
 * Change directory to the application root directory.
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
include 'init_autoloader.php';

// Set timezone
date_default_timezone_set('Europe/Oslo');

// Run the application!
Zend\Mvc\Application::init(include 'config/application.config.php')->run();

?>
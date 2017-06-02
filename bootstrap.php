<?php
use Core\Application;
use Core\Exception\ExceptionInterface;

// require composer autoloader
require_once 'vendor/autoload.php';

// display_errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', getcwd());

try {

    // boot application
    Application::boot();
    // dispatch resource
    Application::dispatch();

} catch (ExceptionInterface $e) {
    $e->display();
}

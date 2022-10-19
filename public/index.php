<?php

require_once '../vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler('\Core\Errors::errorHandler');
set_exception_handler('\Core\Errors::exceptionHandler');

session_start();

$router = new Core\Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

//$router->add('{controller}', ['action' => 'index']);
$router->dispatch($_SERVER['QUERY_STRING']);
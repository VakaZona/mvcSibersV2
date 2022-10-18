<?php

require_once '../vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler('\Core\Errors::errorHandler');
set_exception_handler('\Core\Errors::exceptionHandler');

$router = new Core\Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

$router->dispath($_SERVER['QUERY_STRING']);
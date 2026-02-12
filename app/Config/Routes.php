<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Tasks::index');
$routes->get('tasks/index', 'Tasks::index');
$routes->post('tasks/create', 'Tasks::create');
$routes->post('tasks/update/(:num)', 'Tasks::update/$1');
$routes->post('tasks/delete/(:num)', 'Tasks::delete/$1');
$routes->get('tasks/history', 'Tasks::history');


$routes->get('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/loginProcess', 'Auth::process_login');
$routes->post('auth/registerProcess', 'Auth::process_register');
$routes->get('auth/logout', 'Auth::logout');

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Products::dashboard');

// Pharmacy Routes
$routes->get('categories', 'Categories::index');
$routes->post('categories/create', 'Categories::create');
$routes->post('categories/update', 'Categories::update');
$routes->get('categories/delete/(:num)', 'Categories::delete/$1');

$routes->get('vendors', 'Vendors::index');
$routes->post('vendors/create', 'Vendors::create');
$routes->post('vendors/update', 'Vendors::update');
$routes->get('vendors/delete/(:num)', 'Vendors::delete/$1');

$routes->get('products', 'Products::index');
$routes->get('products/add', 'Products::add');
$routes->post('products/create', 'Products::create');
$routes->get('products/edit/(:num)', 'Products::edit/$1');
$routes->post('products/update', 'Products::update');
$routes->get('products/delete/(:num)', 'Products::delete/$1');

$routes->get('stocks/purchase', 'Stocks::purchase');
$routes->get('stocks/add', 'Stocks::add');
$routes->post('stocks/add_purchase', 'Stocks::add_purchase');
$routes->get('stocks/delete_purchase/(:num)', 'Stocks::delete_purchase/$1');
$routes->post('stocks/update_purchase', 'Stocks::update_purchase');
$routes->get('stocks/purchase_invoice/(:num)', 'Stocks::purchase_invoice/$1');
$routes->get('stocks/sales', 'Stocks::sales');
$routes->get('stocks/invoice/(:num)', 'Stocks::invoice/$1'); // Added invoice print route
$routes->get('stocks/report', 'Stocks::sales_report');
$routes->post('stocks/process_sale', 'Stocks::process_sale');


$routes->get('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/loginProcess', 'Auth::process_login');
$routes->post('auth/registerProcess', 'Auth::process_register');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/profile', 'Auth::profile');
$routes->post('auth/updatePassword', 'Auth::updatePassword');

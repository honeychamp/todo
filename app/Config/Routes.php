<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Products::dashboard');
$routes->get('analytics', 'Analytics::index');

// Pharmacy Routes
$routes->get('categories', 'Categories::index');
$routes->post('categories/create', 'Categories::create');
$routes->post('categories/update', 'Categories::update');
$routes->get('categories/delete/(:num)', 'Categories::delete/$1');

$routes->get('vendors', 'Vendors::index');
$routes->post('vendors/create', 'Vendors::create');
$routes->post('vendors/update', 'Vendors::update');
$routes->get('vendors/delete/(:num)', 'Vendors::delete/$1');

$routes->get('expenses', 'Expenses::index');
$routes->get('expenses/export', 'Expenses::export_expenses');
$routes->post('expenses/create', 'Expenses::create');
$routes->get('expenses/delete/(:num)', 'Expenses::delete/$1');

$routes->get('products', 'Products::index');
$routes->get('products/shortage', 'Products::shortage_list');
$routes->get('products/add', 'Products::add');
$routes->post('products/create', 'Products::create');
$routes->get('products/edit/(:num)', 'Products::edit/$1');
$routes->post('products/update', 'Products::update');
$routes->get('products/delete/(:num)', 'Products::delete/$1');

// Purchases Module
$routes->get('purchases', 'Purchases::index');
$routes->get('purchases/select_vendor', 'Purchases::select_vendor');
$routes->get('purchases/add/(:num)', 'Purchases::add/$1');
$routes->post('purchases/process_add', 'Purchases::process_add');
$routes->get('purchases/delete/(:num)', 'Purchases::delete/$1');
$routes->post('purchases/update', 'Purchases::update');
$routes->get('purchases/vendor/(:num)', 'Purchases::vendor_history/$1');
$routes->get('purchases/invoice/(:num)', 'Purchases::invoice/$1');
$routes->get('purchases/dues', 'Purchases::dues');
$routes->post('purchases/add_payment', 'Purchases::add_payment');
$routes->get('purchases/delete_payment/(:num)', 'Purchases::delete_payment/$1');

// Sales Module
$routes->get('sales', 'Sales::index');
$routes->get('sales/inventory', 'Sales::inventory');
$routes->get('sales/invoice/(:num)', 'Sales::invoice/$1');
$routes->get('sales/report', 'Sales::report');
$routes->get('sales/export', 'Sales::export');
$routes->get('sales/history', 'Sales::history');

$routes->get('sales/void/(:num)', 'Sales::void/$1');
$routes->post('sales/process', 'Sales::process');



$routes->get('settings', 'Settings::index');
$routes->post('settings/update', 'Settings::update');

$routes->get('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/loginProcess', 'Auth::process_login');
$routes->post('auth/registerProcess', 'Auth::process_register');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/profile', 'Auth::profile');
$routes->post('auth/updatePassword', 'Auth::updatePassword');

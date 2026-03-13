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
$routes->get('vendors/add', 'Vendors::add');
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
$routes->get('products/view/(:num)', 'Products::view/$1');
$routes->post('products/update', 'Products::update');
$routes->get('products/delete/(:num)', 'Products::delete/$1');

// Purchases Module
$routes->get('purchases', 'Purchases::index');
$routes->get('purchases/select_vendor', 'Purchases::select_vendor');
$routes->get('purchases/add/(:num)', 'Purchases::add/$1');
$routes->post('purchases/process_add', 'Purchases::process_add');
$routes->get('purchases/view/(:num)', 'Purchases::view_purchase/$1');
$routes->get('purchases/edit/(:num)', 'Purchases::edit/$1');
$routes->post('purchases/update', 'Purchases::update');
    $routes->get('purchases/export', 'Purchases::export_csv');
    $routes->get('purchases/status/(:num)/(:any)', 'Purchases::update_status/$1/$2');
    $routes->get('purchases/delete/(:num)', 'Purchases::delete/$1');
$routes->get('purchases/delete_item/(:num)', 'Purchases::delete_item/$1');
$routes->post('purchases/update_item', 'Purchases::update_item');
$routes->post('purchases/add_item', 'Purchases::add_item');
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



// Doctors Module
$routes->get('doctors', 'Doctors::index');
$routes->get('doctors/add', 'Doctors::add');
$routes->post('doctors/create', 'Doctors::create');
$routes->get('doctors/ledger/(:num)', 'Doctors::ledger/$1');
$routes->post('doctors/add_payment', 'Doctors::add_payment');
$routes->get('doctors/payments', 'Doctors::payments');
$routes->get('doctors/delete/(:num)', 'Doctors::delete/$1');

$routes->get('settings', 'Settings::index');
$routes->post('settings/update', 'Settings::update');

$routes->get('auth/login', 'Auth::login');

$routes->post('auth/loginProcess', 'Auth::process_login');

$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/profile', 'Auth::profile');
$routes->post('auth/updatePassword', 'Auth::updatePassword');

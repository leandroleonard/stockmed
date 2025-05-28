<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('/dashboard', function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('storage', 'StorageController::index');
    $routes->get('stock', 'ProductController::index');
    $routes->group('clients', function($routes){
        $routes->get('/', 'CustomerController::index');
        $routes->get('create', 'CustomerController::create');
    });
    $routes->get('buy', 'BuyController::index');
    $routes->get('sales', 'SalesController::index');
    $routes->get('suppliers', 'SupplierController::index');
});

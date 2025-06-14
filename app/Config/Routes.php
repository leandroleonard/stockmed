<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('/dashboard', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('storage', 'StorageController::index');
    $routes->group('stock', function($routes){
        $routes->get('/', 'ProductController::index');
        $routes->get('create', 'ProductController::form');
        $routes->post('add', 'ProductController::submit');
    });
    $routes->group('clients', function($routes){
        $routes->get('/', 'CustomerController::index');
        $routes->get('create', 'CustomerController::create');
        $routes->post('submit', 'Entities\CustomerController::create');
        $routes->post('update', 'Entities\CustomerController::update');
        $routes->get('(:any)', 'CustomerController::update/$1');
    });
    $routes->get('buy', 'BuyController::index');
    $routes->get('sales', 'SalesController::index');
    $routes->get('suppliers', 'SupplierController::index');
});

$routes->get('login', 'Auth\AuthController::login');
$routes->post('login', 'Auth\AuthController::authenticate');
$routes->get('logout', 'Auth\AuthController::logout');

// include 'RoutesTest.php';

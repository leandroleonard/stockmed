<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Aplica o filtro 'auth' a todo o grupo dashboard
$routes->group('/dashboard', ['filter' => 'auth'], function ($routes) {
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

// Rotas de autenticação (sem filtro)
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

include 'RoutesTest.php';

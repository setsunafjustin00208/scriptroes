<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// For the Views part of the poge and its processes in SSR

$routes->get('/', 'Home::index');
$routes->get('login', 'Pages\Login::index');

$routes->group('user', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->options('(:any)', function() {
        return service('response')
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setStatusCode(204);
    });
    $routes->post('register', 'UserController::register', ['filter' => 'cors']);
    $routes->post('login', 'UserController::login', ['filter' => 'cors']);
    $routes->post('logout', 'UserController::logout', ['filter' => 'cors']);
    $routes->put('update/(:segment)', 'UserController::update/$1', ['filter' => 'cors']);
    $routes->delete('delete/(:segment)', 'UserController::delete/$1', ['filter' => 'cors']);
});





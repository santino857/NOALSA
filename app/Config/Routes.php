<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. RUTAS PÚBLICAS: solo el login (no hay registro abierto)
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::processLogin');
$routes->post('logout', 'Auth::logout');

// 2. RUTAS PROTEGIDAS: requieren haber iniciado sesión
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
    $routes->get('/', 'Home::index');

    // Clientes
    $routes->get('clientes', 'Clientes::index');
    $routes->get('clientes/nuevo', 'Clientes::nuevo');
    $routes->post('clientes', 'Clientes::crear');
    $routes->get('clientes/(:num)', 'Clientes::ver/$1');
    $routes->get('clientes/(:num)/editar', 'Clientes::editar/$1');
    $routes->post('clientes/(:num)', 'Clientes::actualizar/$1');
    $routes->post('clientes/(:num)/eliminar', 'Clientes::eliminar/$1');

    // Productos y precios
    $routes->get('productos', 'Productos::index');
    $routes->get('productos/nuevo', 'Productos::nuevo');
    $routes->post('productos', 'Productos::crear');
    $routes->get('productos/(:num)/editar', 'Productos::editar/$1');
    $routes->post('productos/(:num)', 'Productos::actualizar/$1');
    $routes->post('productos/(:num)/alternar', 'Productos::alternar/$1');
    $routes->post('productos/(:num)/eliminar', 'Productos::eliminar/$1');

    // Ventas y pagos
    $routes->get('ventas', 'Ventas::index');
    $routes->get('ventas/nueva', 'Ventas::nueva');
    $routes->post('ventas', 'Ventas::crear');
    $routes->post('ventas/(:num)/eliminar', 'Ventas::eliminar/$1');
    $routes->post('ventas/(:num)/pagos', 'Pagos::crear/$1');
    $routes->post('pagos/(:num)/eliminar', 'Pagos::eliminar/$1');
});

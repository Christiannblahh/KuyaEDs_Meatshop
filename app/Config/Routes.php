<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Kuya EDs Meatshop routes
$routes->group('', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes): void {
    $routes->get('order', 'Order::index');
    $routes->get('order/cart', 'Order::cart');
    $routes->post('order/addToCart', 'Order::addToCart');
    $routes->post('order/updateCart', 'Order::updateCart');
    $routes->post('order/removeFromCart', 'Order::removeFromCart');
    $routes->post('order/checkout', 'Order::checkout');

    $routes->get('products', 'Products::index');
    $routes->match(['get', 'post'], 'products/create', 'Products::create');
    $routes->match(['get', 'post'], 'products/edit/(:num)', 'Products::edit/$1');
    $routes->get('products/delete/(:num)', 'Products::delete/$1');

    $routes->get('inventory', 'Inventory::index');
    $routes->match(['get', 'post'], 'inventory/add', 'Inventory::addStock');
    $routes->match(['get', 'post'], 'inventory/quick-add', 'Inventory::quickAdd');

    $routes->match(['get', 'post'], 'sales/create', 'Sales::create');

    $routes->get('reports/sales', 'Reports::sales');
    $routes->get('reports/alerts', 'Reports::alerts');
    
    // Image serving route for uploaded product images
    $routes->get('image/(:any)', 'Products::serveImage/$1');
});

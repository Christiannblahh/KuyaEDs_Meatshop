<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Kuya EDs Meatshop routes
$routes->group('', ['namespace' => 'App\Controllers'], static function (RouteCollection $routes): void {
    // Customer Order System
    $routes->get('order', 'Order::index');
    $routes->get('order/orders', 'Order::orders');
    $routes->get('order/viewOrder', 'Order::viewOrder');
    $routes->post('order/addToOrder', 'Order::addToOrder');
    $routes->post('order/updateOrder', 'Order::updateOrder');
    $routes->post('order/removeFromOrder', 'Order::removeFromOrder');
    $routes->post('order/processPayment', 'Order::processPayment');
    $routes->get('order/receipt/(:num)', 'Order::receipt/$1');
    $routes->get('order/printReceipt/(:num)', 'Order::printReceipt/$1');
    $routes->post('order/clearOrder', 'Order::clearOrder');
    
    // Backward compatibility for cart methods (still used by view_order.php)
    $routes->post('order/updateCart', 'Order::updateCart');
    $routes->post('order/removeFromCart', 'Order::removeFromCart');

    $routes->get('products', 'Products::index');
    $routes->match(['get', 'post'], 'products/create', 'Products::create');
    $routes->match(['get', 'post'], 'products/edit/(:num)', 'Products::edit/$1');
    $routes->get('products/delete/(:num)', 'Products::delete/$1');

    $routes->get('inventory', 'Inventory::index');
    $routes->match(['get', 'post'], 'inventory/add', 'Inventory::addStock');
    $routes->match(['get', 'post'], 'inventory/quick-add', 'Inventory::quickAdd');
    $routes->post('inventory/discard-batch', 'Inventory::discardBatch');

    $routes->match(['get', 'post'], 'sales/create', 'Sales::create');

    $routes->get('reports/sales', 'Reports::sales');
    $routes->get('reports/alerts', 'Reports::alerts');
    
    // Image serving route for uploaded product images
    $routes->get('image/(:any)', 'Products::serveImage/$1');
});

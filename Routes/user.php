<?php

use App\Routes\Router;
use App\Middleware\UserOnlyMiddleware;
use App\Controllers\UserController;
use App\Controllers\OrderController;


$user = new UserController();
$order = new OrderController();


Router::group('/v1', function () use ($user, $order) {
    
    Router::add('GET', '/users/{id}', [$user, 'getProfile']); 
    Router::add('POST', '/users/{id}', [$user, 'updateProfile']);
    Router::add('PUT', '/users/{userid}/password', [$user, 'changePassword']);

    #Order Routes
    Router::add('POST', '/orders', [$order, 'createOrder']);
    Router::add('GET', '/orders', [$order, 'getOrders']);
    Router::add('PATCH', '/orders/{id}', [$order, 'updateOrderStatus']);



   
   
}, [UserOnlyMiddleware::class]);

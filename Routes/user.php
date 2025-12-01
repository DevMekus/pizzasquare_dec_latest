<?php

use App\Routes\Router;
use App\Middleware\UserOnlyMiddleware;
use App\Controllers\UserController;


$user = new UserController();


Router::group('/v1', function () use ($user) {
    
    Router::add('GET', '/users/{id}', [$user, 'getProfile']); 
    Router::add('POST', '/users/{id}', [$user, 'updateProfile']);
    Router::add('PUT', '/users/{userid}/password', [$user, 'changePassword']);

   
   
}, [UserOnlyMiddleware::class]);

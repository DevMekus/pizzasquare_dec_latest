<?php

use App\Routes\Router;
use App\Middleware\GuestOnlyMiddleware;
use App\Controllers\UserController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\SizesController;
use App\Controllers\CategorySizeStockController;
use App\Controllers\ExtrasController;
use App\Controllers\ProductStockController;
use App\Controllers\DealController;
use App\Controllers\CityController;
use App\Controllers\CouponController;
use App\Controllers\Geocode;
use App\Controllers\PaymentController;

$user = new UserController();
$category = new CategoryController();
$product = new ProductController();
$sizes = new SizesController();
$cstock = new CategorySizeStockController();
$order = new OrderController();
$xtra = new ExtrasController();
$productStock = new ProductStockController();
$deal = new DealController();
$city = new CityController();
$coupon = new CouponController();
$geolocation = new Geocode();
$payment = new PaymentController();


Router::group('v1', function () use (
   $user,
   $category,
   $product,
    $order,
    $sizes,
    $cstock,
    $xtra,
    $productStock,
    $deal,
    $city,
    $coupon,
    $geolocation,
    $payment
) {
    #User Routes
    Router::add('POST', '/auth/login', [$user, 'login']); 
    Router::add('POST', '/auth/register', [$user, 'register']); 
    Router::add('POST', '/auth/logout', [$user, 'logout']); 
    Router::add('POST', '/auth/recover', [$user, 'recoverAccount']); 
    Router::add('POST', '/auth/reset', [$user, 'resetPassword']); 
 

    #Category Routes
    Router::add('GET', '/categories', [$category, 'index']);
    Router::add('GET', '/categories/{id}', [$category, 'show']);

    #Product Routes
    Router::add('GET', '/products', [$product, 'index']);
    Router::add('GET', '/products/{id}', [$product, 'show']);
    Router::add('GET', '/products/full/{id}', [$product, 'showFull']);
    Router::add('GET', '/pizzas-with-sizes', [$product, 'pizzasWithSizes']);


    #Sizes Routes
    Router::add('GET', '/sizes', [$sizes, 'index']);
    Router::add('GET', '/sizes/{id}', [$sizes, 'show']);

    Router::add('GET', '/c-stock', [$cstock, 'index']);
     Router::add('GET', '/c-stock/{id}', [$cstock, 'show']);
    #Extras Routes
    Router::add('GET', '/extras', [$xtra, 'listExtras']);
    Router::add('GET', '/extras/{id}', [$xtra, 'getExtraById']);
    
    #DealRoutes
    Router::add('GET', '/deals', [$deal, 'listDeals']);
    Router::add('GET', '/deals/{id}', [$deal, 'getDealById']);

     #CityRoutes
    Router::add('GET', '/city', [$city, 'listCities']);
    Router::add('GET', '/city/{id}', [$city, 'fetchCityById']);

     #CouponRoutes
    Router::add('GET', '/coupon', [$coupon, 'listCoupons']);
    Router::add('GET', '/coupon/{id}', [$coupon, 'getCouponById']);

    #Order Routes
    Router::add('POST', '/orders/create', [$order, 'createOrder']);
    Router::add('GET', '/vat', [$order, 'listVat']);
    // Router::add('GET', '/order/{id}', [$order, 'listVat']);

    #Geocode Routes
    Router::add('POST', '/geocode', [$geolocation, 'reverseGeocode']); 
    
    #Payment Routes
    Router::add('POST', '/payment/confirm', [$payment, 'confirmPayment']);

   

}, [GuestOnlyMiddleware::class]);

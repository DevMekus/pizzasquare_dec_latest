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
use App\Controllers\NewsUpdateController;
use App\Controllers\CityController;
use App\Controllers\CouponController;
use App\Controllers\Geocode;
use App\Controllers\PaymentController;
use App\Controllers\PromotionsController;
use App\Controllers\SalesAnalyticsController;
use App\Controllers\ReverseGeocode;

$user = new UserController();
$category = new CategoryController();
$product = new ProductController();
$sizes = new SizesController();
$cstock = new CategorySizeStockController();
$order = new OrderController();
$xtra = new ExtrasController();
$productStock = new ProductStockController();
$newsUpdate = new NewsUpdateController();
$city = new CityController();
$coupon = new CouponController();
$geolocation = new Geocode();
$payment = new PaymentController();
$promotion = new PromotionsController();

$salesAnalytics = new SalesAnalyticsController();

$reverseGeocode = new ReverseGeocode();



Router::group('v1', function () use (
   $user,
   $category,
   $product,
    $order,
    $sizes,
    $cstock,
    $xtra,
    $productStock,
    $newsUpdate,
    $city,
    $coupon,
    $geolocation,
    $payment,
    $promotion,
    $salesAnalytics,
    $reverseGeocode
) {
    #User Routes
    Router::add('POST', '/auth/login', [$user, 'login']); 
    Router::add('POST', '/auth/register', [$user, 'register']); 
    Router::add('POST', '/auth/logout', [$user, 'logout']); 
    Router::add('POST', '/auth/recover', [$user, 'recoverAccount']); 
    Router::add('POST', '/auth/reset', [$user, 'resetPassword']); 
    Router::add('POST',  '/contact', [$user, 'guestMessaging']);
 

    #Category Routes
    Router::add('GET', '/categories', [$category, 'index']);
    Router::add('GET', '/categories/{id}', [$category, 'show']);

    #Product Routes
    Router::add('GET', '/products', [$product, 'index']);
    Router::add('GET', '/products/{id}', [$product, 'show']);
    Router::add('GET', '/products/full/{id}', [$product, 'showFull']);
    Router::add('GET', '/pizzas-with-sizes', [$product, 'pizzasWithSizes']);
    Router::add('GET', '/products/ingredients/{id}', [$product, 'getIngredientsByProduct']);

    #Sizes Routes
    Router::add('GET', '/sizes', [$sizes, 'index']);
    Router::add('GET', '/sizes/{id}', [$sizes, 'show']);

    Router::add('GET', '/c-stock', [$cstock, 'index']);
     Router::add('GET', '/c-stock/{id}', [$cstock, 'show']);
    #Extras Routes
    Router::add('GET', '/extras', [$xtra, 'listExtras']);
    Router::add('GET', '/extras/{id}', [$xtra, 'getExtraById']);
    
    #NewsUpdateRoutes
    Router::add('GET', '/news_updates', [$newsUpdate, 'listNewsUpdates']);
    Router::add('GET', '/news_updates/{id}', [$newsUpdate, 'getNewsUpdateById']);

     #CityRoutes
    Router::add('GET', '/city', [$city, 'listCities']);
    Router::add('GET', '/city/{id}', [$city, 'fetchCityById']);

     #CouponRoutes
    Router::add('GET', '/coupon', [$coupon, 'listCoupons']);
    Router::add('GET', '/coupon/{id}', [$coupon, 'getCouponById']);

    #Order Routes
    Router::add('POST', '/orders/create', [$order, 'createOrder']);
    Router::add('GET', '/vat', [$order, 'listVat']);
    Router::add('GET', '/orders/{id}', [$order, 'getOrder']);
    

    #Geocode Routes
    Router::add('POST', '/geocode', [$geolocation, 'reverseGeocode']); 
    Router::add('POST', '/reverse-geocode', [$reverseGeocode, 'reverseGeocode']); 
    
    #Payment Routes
    Router::add('POST', '/payment/confirm', [$payment, 'confirmPayment']);

    #PromotionRoutes
    Router::add('GET', '/promotions', [$promotion, 'listPromotions']);
    Router::add('GET', '/promotions/{id}', [$promotion, 'getPromotionById']);

    #Sales Analytics Routes
    Router::add('GET', '/analytics/top-dishes', [$salesAnalytics, 'getTopDishes']);

   

}, [GuestOnlyMiddleware::class]);

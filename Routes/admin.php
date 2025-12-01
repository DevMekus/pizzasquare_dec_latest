<?php

use App\Routes\Router;
use App\Middleware\AdminOnlyMiddleware;
use App\Controllers\CategoryController;
use App\Controllers\SizesController;
use App\Controllers\CategorySizeStockController;
use App\Controllers\ProductController;
use App\Controllers\ProductSizesController;
use App\Controllers\ProductStockController;
use App\Controllers\DealController;
use App\Controllers\ExtrasController;
use App\Controllers\CityController;
use App\Controllers\CouponController;
use App\Controllers\UserController;
use App\Controllers\ActivityController;


$category = new CategoryController();
$sizes = new SizesController();
$cstock = new CategorySizeStockController();
$product = new ProductController();
$productSizes = new ProductSizesController();
$productStock = new ProductStockController();
$deal = new DealController();
$extras = new ExtrasController();
$city = new CityController();
$coupon = new CouponController();
$user = new UserController();
$activity = new ActivityController();



Router::group('v1/admin', function () use (  
    $category,
    $sizes,
    $cstock,
    $product,
    $productSizes,
    $productStock,
    $deal,
    $extras,
    $city,
    $coupon,
    $user,
    $activity
) {

    #Category Routes   
    Router::add('POST', '/categories', [$category, 'store']);
    Router::add('PUT', '/categories/{id}', [$category, 'update']);
    Router::add('DELETE', '/categories/{id}', [$category, 'destroy']);

    #Sizes Routes   
    Router::add('POST', '/sizes', [$sizes, 'store']);
    Router::add('PUT', '/sizes/{id}', [$sizes, 'update']);
    Router::add('DELETE', '/sizes/{id}', [$sizes, 'destroy']);

    #Category Size Stock Routes
    Router::add('GET', '/c-stock', [$cstock, 'index']);
    Router::add('GET', '/c-stock/{id}', [$cstock, 'show']);
    Router::add('POST', '/c-stock', [$cstock, 'create']);
    Router::add('PUT', '/c-stock/{id}', [$cstock, 'update']);
    Router::add('DELETE', '/c-stock/{id}', [$cstock, 'destroy']);

    #Product Routes
    Router::add('POST', '/products', [$product, 'store']);
    Router::add('POST', '/products/{id}', [$product, 'update']); 
    Router::add('DELETE', '/products/{id}', [$product, 'delete']);

    #Product Sizes Routes
    Router::add('POST', '/product-sizes', [$productSizes, 'addSizesBulk']);
    Router::add('PUT', '/product-sizes/{id}', [$productSizes, 'updateSize']);
    Router::add('DELETE', '/product-sizes/{id}', [$productSizes, 'deleteSize']);

    #Product Stock Routes
    Router::add('GET', '/product-stocks/{product_id}', [$productStock, 'productStocks']);   
    Router::add('GET', '/category-stock/{category_id}', [$productStock, 'categoryStocks']);  
    Router::add('GET', '/product-stocks', [$productStock, 'allProductStocks']);  
    Router::add('PUT', '/product-stock/{stock_id}/update', [$productStock, 'adjustProductStock']);
    Router::add('PUT', '/category-stock/{stock_id}/update', [$productStock, 'adjustCategoryStock']);


    #Deal Routes
    Router::add('POST', '/deals', [$deal, 'postDeal']);
    Router::add('POST', '/deals/{id}', [$deal, 'updateDeal']);
    Router::add('DELETE', '/deals/{id}', [$deal, 'deleteDeal']);

    #ExtrasRoutes
    Router::add('POST', '/extras', [$extras, 'postExtras']);
    Router::add('PUT', '/extras/{id}', [$extras, 'updateExtras']);
    Router::add('DELETE', '/extras/{id}', [$extras, 'deleteExtras']);

    #CityRoutes
    Router::add('POST', '/city', [$city, 'postCity']);
    Router::add('PATCH', '/city/{id}', [$city, 'updateCity']);
    Router::add('DELETE', '/city/{id}', [$city, 'deleteCity']);

     #CouponRoutes
    Router::add('POST', '/coupon', [$coupon, 'postCoupon']);
    Router::add('PATCH', '/coupon/{id}', [$coupon, 'updateCoupon']);
    Router::add('DELETE', '/coupon/{id}', [$coupon, 'deleteCoupon']);

    #UserRoutes
    Router::add('GET', '/users', [$user, 'getprofiles']); 
    Router::add('GET', '/users/{id}', [$user, 'getProfile']); 
    Router::add('POST', '/users', [$user, 'register']);

     #ActivityController
    Router::add('GET',  '/log', [$activity, 'listActivities']);
    Router::add('POST',  '/log', [$activity, 'postActivity']);
    Router::add('DELETE',  '/log/{id}', [$activity, 'deleteActivity']);
    


    

}, [AdminOnlyMiddleware::class]);

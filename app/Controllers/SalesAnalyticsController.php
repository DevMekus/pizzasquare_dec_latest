<?php

namespace App\Controllers;  
use App\Services\SalesAnalyticsService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;
use App\Services\OrderService;

class SalesAnalyticsController{

    public function getTopDishes(){
       try {
            $orders = OrderService::fetchAllOrders();          

            if (empty($orders))
                Response::error(404, "No orders found");
            
            $topDishes = SalesAnalyticsService::calculateTopDishes($orders);
            Response::success($topDishes, "Top dishes retrieved successfully");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'SalesAnalyticsController::getTopDishes', [], $e);
            Response::error(500, "Error fetching top dishes");
        }
        
    }
}
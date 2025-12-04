<?php
namespace App\Controllers;  
use App\Services\OrderService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;


class OrderController{

    public function getOrders(){
        // Code to list all orders
    }
    public function createOrder(){
        $data = RequestValidator::validate([
            'order_id' => 'required|address',
            'total_amount' => 'required|address',
            'cart' => 'required|address',
        ]);
        $data = RequestValidator::sanitize($data);
        try {
            $exists = OrderService::fetchOrderById($data['order_id']);
            if ($exists) {
                Response::error(409, "Order with this ID already exists");
            }
            
            $newOrderId = OrderService::createNewOrder($data);
            Response::success(['order_id' => $newOrderId], "Order created successfully");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'OrderController::createOrder', ['OrderData' => json_encode($data)], $e);
            Response::error(500, "Error creating order");
        }
    }

    public function getOrder($order_id){
        // Code to retrieve order details by order ID
    }

    public function getOrdersByUser($user_id){
        // Code to retrieve all orders for a given user ID
    }

    public function updateOrderStatus($order_id, $status){
        // Code to update the status of an order
    }

    public function cancelOrder($order_id){
        // Code to cancel an order by order ID and handle stock rollback
    }

    public function listVat()
    {
        try {
            $vat = OrderService::fetchVAT();

            if (empty($vat))
                Response::error(404, "vat not found");
            Response::success($vat, "vat found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'OrderController::listVat', [], $e);
            Response::error(500, "Error fetching vat");
        }
    }
}
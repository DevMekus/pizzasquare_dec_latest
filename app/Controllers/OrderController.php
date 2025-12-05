<?php
namespace App\Controllers;  
use App\Services\OrderService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;


class OrderController{

    public function getOrders(){
       try {
            $orders = OrderService::fetchAllOrders();

            if (empty($orders))
                Response::error(404, "No orders found");
            Response::success($orders, "Orders retrieved successfully");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'OrderController::getOrders', [], $e);
            Response::error(500, "Error fetching orders");
        }
        
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
        try {
            $order = OrderService::fetchOrderById($order_id);
            if (empty($order))
                Response::error(404, "Order not found");
            Response::success($order, "Order found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'OrderController::getOrder', ['OrderID' => $order_id], $e);
            Response::error(500, "Error fetching order");
        }
       
    }

    
    public function updateOrderStatus($id){
       try {
            $id = RequestValidator::parseId($id);
            $data = RequestValidator::validate([
                'status'     => 'require|min:3',
            ]);
            $data = RequestValidator::sanitize($data);
            $order = OrderService::fetchOrderById($id);
            
            if (empty($order))
                Response::error(409, "order not found");

            if (OrderService::updateOrderStatus($id, $data, $order))
                Response::success([], "order updated");
            
       } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'OrderController::updateOrderStatus', [], $e);
            Response::error(500, "An error occurred while updating order");
       }
    }

    public function deleteOrder($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $order = OrderService::fetchOrderById($id);

            if (empty($order))
                Response::error(409, "order not found");

            if (OrderService::deleteOrder($id))
                Response::success([], "order deleted");
        } catch (\Exception $e) {
            Utility::log($e->getMessage(), 'error', 'OrderController::deleteOrder', [], $e);
            Response::error(500, "An error occurred while deleting order");
        }
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
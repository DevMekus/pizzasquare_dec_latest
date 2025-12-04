<?php
namespace App\Services;
use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;
use App\Utils\MailClient;

class OrderService{

    public static function fetchVAT(){
         $vat_tbl = Utility::$vat_tbl;

        try {
            return Database::joinTables(
                "$vat_tbl v",
                [],
                [
                    "v.*"
                ],
                [],

            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'OrderService::fetchVAT', ['Order' => ''], $th);
            Response::error(500, "An error occurred while fetching VAT");
        }
    }

    public static function fetchOrderById($order_id){
        $order = Utility::$orders;

        try {
            return Database::findWhere($order, [
                "order_id" => $order_id
            ]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'OrderService::fetchOrderById', ['OrderID' => $order_id], $th);
            Response::error(500, "An error occurred while fetching order");
        }
    }

    public static function createNewOrder($orderData){
        $order = Utility::$orders;
        $order_items = Utility::$order_items;
        $payments = Utility::$payments;
        $order_toppings = Utility::$order_toppings;

        try {
           Database::beginTransaction();
            
           $orderUpload = [
                'order_id' => $orderData['order_id'],
                'userid' => $orderData['userid'] ?? null,
                'customer_name' => $orderData['customer_name'] ?? null,
                'customer_phone' => $orderData['customer_phone'] ?? null,
                'customer_type' => isset($orderData['customer_type']) ? strtolower($orderData['customer_type']) : 'walk_in',
                'order_note' => $orderData['order_note'] ?? null,
                'delivery' => isset($orderData['delivery_type']) ? strtolower($orderData['delivery_type']) : 'pickup',
                'delivery_address' => isset($orderData['delivery_address']) ? $orderData['delivery_address'].", ".$orderData['city'] : null,
                'status' => 'pending',
                'total' => $orderData['total_amount'] ?? 0,
                'attendant' => $orderData['attendant'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $newOrderId = Database::insert($order, $orderUpload);

            //process cart here
            foreach($orderData['cart'] as $item){
                
                $itemData = [
                    'order_id' => intval($newOrderId),
                    'product_id' => intval($item['id']),
                    'size_id' => intval($item['size_id']),
                    'unit_price' => floatval($item['price']),
                    'qty' => intval($item['qty']),
                    'subtotal' => floatval($item['price']) * intval($item['qty']),
                ];
                Database::insert($order_items, $itemData);

                if(isset($item['toppings']) && is_array($item['toppings'])){
                    foreach($item['toppings'] as $topping){
                        $toppingData = [
                            'order_id' => $newOrderId,
                            'product_id' => $item['id'],
                            'topping' => $topping['extras'],
                            'size_id' => $item['size_id'],
                            'unit_price' => intval($topping['price']),
                            'qty' => $item['qty'],
                            'subtotal' => intval($topping['price']) * intval($item['qty']),
                        ];
                        Database::insert($order_toppings, $toppingData);
                    }
                }

                //check if its a product_stock or category_size_stock and reduce stock accordingly
            //    $status = ProductStockService::reduceAuto(
            //         $item['id'],
            //         $item['size_id'],
            //         $item['qty']
            //     );

            //     if ($status === "insufficient_stock") {
            //         // throw new \Exception("Insufficient stock for product {$item['product_id']}");
            //     }               
            }
            
           $payment = $orderData['payment'];        
            //save payment info
            $paymentData = [
                'order_id' => $newOrderId,
                'total_paid' => floatval($payment['total_paid'] ?? 0),
                'payment_type' => $payment['payment_type'] ?? 'single',
                'cash' => floatval($payment['cash'] ?? 0),
                'card' => floatval($payment['card'] ?? 0),
                'transfer' => floatval($payment['transfer'] ?? 0),
                'online' => floatval($payment['online'] ?? 0),
                'item_amount' => floatval($payment['item_amount'] ?? 0),
                'delivery_fee' => floatval($payment['delivery_fee'] ?? 0),
                'payment_date' => date('Y-m-d H:i:s'),
            ];
            
            Database::insert($payments, $paymentData);
            Database::commit();

            // if ($orderData['customer_type'] !=='walk_in'){
            //     EmailServices::sendOrderConfirmationEmail([
            //         'order_id' => $newOrderId,
            //         'customer_email' => $orderData['customer_email'] ?? null,
            //         'customer_name' => $orderData['customer_name'] ?? null,
            //     ]);
                
            // }
            
            // ActivityService::saveActivity([
            //     'userid' => '',
            //     'type' => 'login',
            //     'title' => 'login successful',
            // ]);

            return $newOrderId;

          
        } catch (\Throwable $th) {
            Database::rollBack();
            Utility::log($th->getMessage(), 'error', 'OrderService::createNewOrder', ['Order' => json_encode($orderData)], $th);
            Response::error(500, "An error occurred while creating a new order");
        }
    }

   
    
}
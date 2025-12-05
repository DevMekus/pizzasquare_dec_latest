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


    public static function fetchOrderById($id)
    {
        $orders_tbl         = Utility::$orders;
        $payments_tbl       = Utility::$payments;
        $order_items_tbl    = Utility::$order_items;
        $order_toppings_tbl = Utility::$order_toppings;
        $products_tbl       = Utility::$products;

        try {

            // 1️⃣ Fetch Order + Payment
            $order = Database::joinTables(
                "$orders_tbl o",
                [
                    [
                        "type"  => "LEFT",
                        "table" => "$payments_tbl pay",
                        "on"    => "o.id = pay.order_id"
                    ]
                ],
                [
                    "o.*",
                    "pay.payment_type",
                    "pay.total_paid",
                    "pay.cash",
                    "pay.card",
                    "pay.transfer",
                    "pay.online",
                    "pay.delivery_fee",
                    "pay.item_amount"
                ],
                [
                    "OR" => [
                        "o.id"       => $id,
                        "o.order_id" => $id,
                        "o.userid"   => $id,
                    ]
                ]
            );

            if (!$order) return false;

            $order = $order[0]; // single row



            // 2️⃣ Fetch Order Items + Product Data
            $items = Database::joinTables(
                "$order_items_tbl oi",
                [
                    [
                        "type"  => "LEFT",
                        "table" => "$products_tbl p",
                        "on"    => "oi.product_id = p.id"
                    ]
                ],
                [
                    "oi.*",
                    "p.name AS product_name",
                    "p.sku",
                    "p.image",
                    "p.description",
                    "p.category_id",
                    "p.is_active AS product_active"
                ],
                [
                    "oi.order_id" => $order['id']
                ]
            );



            // 3️⃣ Fetch ALL toppings for each item (corrected)
            foreach ($items as $index => $item) {

                $toppings = Database::joinTables(
                    "$order_toppings_tbl ot",
                    [],
                    ["ot.*"],
                    [
                        "ot.order_id"   => $order['id'],
                        "ot.product_id" => $item['product_id'],
                        "ot.size_id"    => $item['size_id']
                    ]
                );

                $items[$index]['toppings'] = $toppings ?: [];
            }



            // 4️⃣ Attach items to the order
            $order['items'] = $items;


            return $order;

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'OrderService::fetchOrderById', ['OrderID' => $id], $th);
            Response::error(500, "An error occurred while fetching order");
        }
    }

    public static function fetchAllOrders()
    {
        $orders_tbl         = Utility::$orders;
        $payments_tbl       = Utility::$payments;
        $order_items_tbl    = Utility::$order_items;
        $order_toppings_tbl = Utility::$order_toppings;
        $products_tbl       = Utility::$products;

        try {

            // 1️⃣ Fetch all orders + payment info
            $orders = Database::joinTables(
                "$orders_tbl o",
                [
                    [
                        "type"  => "LEFT",
                        "table" => "$payments_tbl pay",
                        "on"    => "o.id = pay.order_id"
                    ]
                ],
                [
                    "o.*",
                    "pay.payment_type",
                    "pay.total_paid",
                    "pay.cash",
                    "pay.card",
                    "pay.transfer",
                    "pay.online",
                    "pay.delivery_fee",
                    "pay.item_amount"
                ],
                [] // no filter = fetch ALL
            );

            if (!$orders) return [];

            // 2️⃣ Loop through each order and fetch its items
            foreach ($orders as $key => $order) {

                $items = Database::joinTables(
                    "$order_items_tbl oi",
                    [
                        [
                            "type"  => "LEFT",
                            "table" => "$products_tbl p",
                            "on"    => "oi.product_id = p.id"
                        ]
                    ],
                    [
                        "oi.*",
                        "p.name AS product_name",
                        "p.sku",
                        "p.image",
                        "p.description",
                        "p.category_id",
                        "p.is_active AS product_active"
                    ],
                    [
                        "oi.order_id" => $order['id']
                    ]
                );

                // ensure $items is an array
                if (!$items || !is_array($items)) {
                    $items = [];
                }

                // 3️⃣ For each item get ALL toppings (always as array)
                foreach ($items as $i => $item) {

                    $toppings = Database::joinTables(
                        "$order_toppings_tbl ot",
                        [],
                        ["ot.*"],
                        [
                            "ot.order_id"   => $order['id'],
                            "ot.product_id" => $item['product_id'],
                            "ot.size_id"    => $item['size_id']
                        ]
                    );

                    // ensure toppings is an array (empty array if none)
                    $items[$i]['toppings'] = ($toppings && is_array($toppings)) ? $toppings : [];
                }

                // Attach items back into each order
                $orders[$key]['items'] = $items;
            }

            // 4️⃣ Return final structured array
            return $orders;

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'OrderService::fetchAllOrders', [], $th);
            Response::error(500, "An error occurred while fetching orders");
        }
    }



    public static function createNewOrder($orderData){
        $order = Utility::$orders;
        $order_items = Utility::$order_items;
        $payments = Utility::$payments;
        $order_toppings = Utility::$order_toppings;

        $status = '';
        isset($orderData['customer_type']) && $orderData['customer_type'] === 'walk_in' ? $status = 'delivered' : $status = 'pending';

        try {
           Database::beginTransaction();
            
           $orderUpload = [
                'order_id' => $orderData['order_id'],
                'userid' => $orderData['userid'] ?? null,
                'customer_name' => $orderData['customer_name'] ?? null,
                'customer_phone' => $orderData['customer_phone'] ?? null,
                'customer_email' => $orderData['email_address'] ?? null,
                'customer_type' => isset($orderData['customer_type']) ? strtolower($orderData['customer_type']) : 'walk_in',
                'order_note' => $orderData['order_note'] ?? null,
                'delivery' => isset($orderData['delivery_type']) ? strtolower($orderData['delivery_type']) : 'pickup',
                'delivery_address' => isset($orderData['delivery_address']) ? $orderData['delivery_address'].", ".$orderData['city'] : null,
                'status' => $status,
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
               $status = ProductStockService::reduceAuto(
                    $item['id'],
                    $item['size_id'],
                    $item['qty']
                );

                if ($status === "insufficient_stock") {                   
                    Utility::log("Insufficient stock for product {$item['id']}", 'error', 'OrderService::createNewOrder', ['OrderID' => $newOrderId, 'ProductID' => $item['id']]);
                  
                }               
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
          
            if ($orderData['customer_type'] !=='walk_in'){
               
                EmailServices::sendOrderConfirmationEmail([
                    'order_id' => $orderData['order_id'],
                    'customer_email' => $orderData['email_address'] ?? null,
                    'customer_name' => $orderData['customer_name'] ?? null,
                ]);

                EmailServices::sendOrderNotificationToAdmin([
                    'order_id' => $orderData['order_id'],
                    'customer_email' => $orderData['email_address'] ?? null,
                    'customer_name' => $orderData['customer_name'] ?? null,
                    'customer_phone' => $orderData['customer_phone'] ?? null,
                    'total_amount' => $orderData['total_amount'] ?? 0,
                    'order_details' => json_encode($orderData['cart']),
                ]);
                
            }
            
            ActivityService::saveActivity([
                'userid' => $orderData['userid'] ?? $_SESSION['userid'] ?? null,
                'type' => 'order',
                'title' => 'Order created successfully',
            ]);
            
            Database::commit();


            return $newOrderId;

          
        } catch (\Throwable $th) {
            Database::rollBack();
            Utility::log($th->getMessage(), 'error', 'OrderService::createNewOrder', ['Order' => json_encode($orderData)], $th);
            Response::error(500, "An error occurred while creating a new order");
        }
    }

    public static function updateOrderStatus($id, $data, $prev){
        try {
            //update order status
            $order = Utility::$orders;
            Database::beginTransaction();
            $update = Database::update(
                $order,
                [
                    'status' => isset($data['status']) ? strtolower($data['status']) : $prev['status'],                     
                ],
                [
                    'order_id' => $id
                ]
            );

            if ($prev['customer_email']){
                 EmailServices::sendOrderUpdateNotification([
                    'order_id' => $id,
                    'customer_email' => $prev['customer_email'] ?? null,
                    'customer_name' => $prev['customer_name'] ?? null,
                    'status' => isset($data['status']) ? strtolower($data['status']) : $prev['status'],  
                ]);

            }

           
            Database::commit();

            //log activity
            ActivityService::saveActivity([
                'userid' => $data['userid'] ?? $_SESSION['userid'] ?? null,
                'type' => 'order',
                'title' => "Order status changed from {$prev['status']} to {$data['status']}",
            ]);

            return true;
            
        } catch (\Throwable $th) {
            Database::rollBack();
            Utility::log($th->getMessage(), 'error', 'OrderService::updateOrderStatus', ['OrderID' => $id, 'UpdateData' => json_encode($data)], $th);
            return false;
          
        }
    }

    public static function deleteOrder($id){
        try {
            $order = Utility::$orders;
            $payments = Utility::$payments;
            $order_items = Utility::$order_items;
            $order_toppings = Utility::$order_toppings;
            Database::beginTransaction();
            
            $delete = Database::delete(
                $order,
                [
                    'id' => $id
                ]
            );
            
            Database::delete(
                $payments,
                [
                    'order_id' => $id
                ]
            );
            
            Database::delete(
                $order_items,
                [
                    'order_id' => $id
                ]
            );
            Database::delete(
                $order_toppings,
                [
                    'order_id' => $id
                ]
            );
           
            Database::commit();
            return $delete;
        } catch (\Throwable $th) {
            Database::rollBack();
            Utility::log($th->getMessage(), 'error', 'OrderService::deleteOrder', ['OrderID' => $id], $th);
            return false;
        }
    }  
    
}
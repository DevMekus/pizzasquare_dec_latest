<?php
namespace App\Services;
use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;
use App\Services\ProductService;

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

    public static function updateVAT($id, $data){
        $vat_tbl = Utility::$vat_tbl;
        //convert vat to percent. Expecting whole number like 10
        $vatInWhole = floatval($data['vat']);
        $vatInPercent = $vatInWhole / 100;

        try {
            $update = Database::update(
                $vat_tbl,
                [
                    'vat' => floatval($vatInPercent),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => $id
                ]
            );

            return $update;

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'OrderService::updateVAT', ['VATID' => $id, 'UpdateData' => json_encode($data)], $th);
            Response::error(500, "An error occurred while updating VAT");
        }
    }


    public static function fetchOrderById($id)
    {
        $orders_tbl         = Utility::$orders;
        $payments_tbl       = Utility::$payments;
        $order_items_tbl    = Utility::$order_items;
        $order_toppings_tbl = Utility::$order_toppings;
        $products_tbl       = Utility::$products;
        $sizes_tbl          = Utility::$sizes;
        $order_removed_ingredients_tbl = Utility::$order_removed_ingredients;

       

        try {

            // 1️⃣ Fetch Order + Payment
            $order = Database::joinTables(
                "$orders_tbl o",
                [
                    [
                        "type"  => "LEFT",
                        "table" => "$payments_tbl pay",
                        "on"    => "o.id = pay.order_id"
                    ],
                   
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
                    "pay.vat",
                    "pay.discount",
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
                    ],
                    [
                        "type"  => "LEFT",
                        "table" => "$sizes_tbl s",
                        "on"    => "oi.size_id = s.id"
                    ]
                    
                ],
                [
                    "oi.*",
                    "p.name AS product_name",
                    "p.sku",
                    "p.image",
                    "p.description",
                    "p.category_id",
                     "s.label AS size_name",
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

            //Join the  $order_removed_ingredients_tbl
            $removedItems = Database::joinTables(
                "$order_removed_ingredients_tbl ori",
                 [],
                 ["ori.*"],
                [
                    "ori.order_id"   => $order['id'],
                  
                ]
            );

            $removedGrouped = [];

            foreach ($removedItems as $ri) {
                $key = $ri['product_id'] . '_' . $ri['size_id'];
                $removedGrouped[$key][] = $ri;
            }


            // Attach removed ingredients to each item
            foreach ($items as $index => $item) {
                $key = $item['product_id'] . '_' . $item['size_id'];
                $items[$index]['removed_ingredients'] = $removedGrouped[$key] ?? [];
            }



            // 4️⃣ Attach items to the order
            $order['items'] = $items;


            return $order;

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'OrderService::fetchOrderById', ['OrderID' => $id], $th);
            Response::error(500, "An error occurred while fetching order");
        }
    }

    public static function fetchOrdersByUser($userId)
    {
        $orders_tbl         = Utility::$orders;
        $payments_tbl       = Utility::$payments;
        $order_items_tbl    = Utility::$order_items;
        $order_toppings_tbl = Utility::$order_toppings;
        $products_tbl       = Utility::$products;
        $sizes_tbl          = Utility::$sizes;
        $order_removed_ingredients_tbl = Utility::$order_removed_ingredients;

        try {

            // 1️⃣ Fetch ALL user orders + payment info
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
                    "pay.vat",
                    "pay.discount",
                    "pay.item_amount"
                ],
                [
                    "o.userid" => $userId
                ],
                ["order" => "o.id DESC"]
            );

            if (!$orders) return [];

            foreach ($orders as $key => $order) {

                // 2️⃣ Fetch items for this order
                $items = Database::joinTables(
                    "$order_items_tbl oi",
                    [
                        [
                            "type"  => "LEFT",
                            "table" => "$products_tbl p",
                            "on"    => "oi.product_id = p.id"
                        ],
                        [
                            "type"  => "LEFT",
                            "table" => "$sizes_tbl s",
                            "on"    => "oi.size_id = s.id"
                        ]
                    ],
                    [
                        "oi.*",
                        "p.name AS product_name",
                        "p.sku",
                        "p.image",
                        "p.description",
                        "p.category_id",
                        "s.label AS size_name",
                        "p.is_active AS product_active"
                    ],
                    [
                        "oi.order_id" => $order['id']
                    ]
                ) ?: [];

                if (!$items) {
                    $orders[$key]['items'] = [];
                    continue;
                }

                // 3️⃣ Fetch toppings ONCE
                $toppingsAll = Database::joinTables(
                    "$order_toppings_tbl ot",
                    [],
                    ["ot.*"],
                    [
                        "ot.order_id" => $order['id']
                    ]
                ) ?: [];

                $toppingsGrouped = [];
                foreach ($toppingsAll as $top) {
                    $gk = $top['product_id'] . '_' . $top['size_id'];
                    $toppingsGrouped[$gk][] = $top;
                }

                // 4️⃣ Fetch removed ingredients ONCE
                $removedAll = Database::joinTables(
                    "$order_removed_ingredients_tbl ori",
                    [],
                    ["ori.*"],
                    [
                        "ori.order_id" => $order['id']
                    ]
                ) ?: [];

                $removedGrouped = [];
                foreach ($removedAll as $ri) {
                    $gk = $ri['product_id'] . '_' . $ri['size_id'];
                    $removedGrouped[$gk][] = $ri;
                }

                // 5️⃣ Attach to items
                foreach ($items as $i => $item) {
                    $gk = $item['product_id'] . '_' . $item['size_id'];
                    $items[$i]['toppings'] = $toppingsGrouped[$gk] ?? [];
                    $items[$i]['removed_ingredients'] = $removedGrouped[$gk] ?? [];
                }

                $orders[$key]['items'] = $items;
            }

            return $orders;

        } catch (\Throwable $th) {
            Utility::log(
                $th->getMessage(),
                'error',
                'OrderService::fetchOrdersByUser',
                ['userId' => $userId],
                $th
            );

            Response::error(500, "An error occurred while fetching user orders");
        }
    }


    
    public static function fetchAllOrders()
    {
        $orders_tbl         = Utility::$orders;
        $payments_tbl       = Utility::$payments;
        $order_items_tbl    = Utility::$order_items;
        $order_toppings_tbl = Utility::$order_toppings;
        $products_tbl       = Utility::$products;
        $sizes_tbl          = Utility::$sizes;
        $order_removed_ingredients_tbl = Utility::$order_removed_ingredients;

        try {

            // 1️⃣ Fetch ALL orders + payment info
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
                    "pay.vat",
                    "pay.discount",
                    "pay.item_amount"
                ],
                [],
                ["order" => "o.id DESC"]
            );

            if (!$orders) return [];

            // 2️⃣ Process each order
            foreach ($orders as $key => $order) {

                // Fetch items
                $items = Database::joinTables(
                    "$order_items_tbl oi",
                    [
                        [
                            "type"  => "LEFT",
                            "table" => "$products_tbl p",
                            "on"    => "oi.product_id = p.id"
                        ],
                        [
                            "type"  => "LEFT",
                            "table" => "$sizes_tbl s",
                            "on"    => "oi.size_id = s.id"
                        ]
                    ],
                    [
                        "oi.*",
                        "p.name AS product_name",
                        "p.sku",
                        "p.image",
                        "p.description",
                        "p.category_id",
                        "s.label AS size_name",
                        "p.is_active AS product_active"
                    ],
                    [
                        "oi.order_id" => $order['id']
                    ]
                ) ?: [];

                // 🔹 If no items, attach empty and continue
                if (!$items) {
                    $orders[$key]['items'] = [];
                    continue;
                }

                // 3️⃣ Fetch ALL toppings for this order (ONCE)
                $toppingsAll = Database::joinTables(
                    "$order_toppings_tbl ot",
                    [],
                    ["ot.*"],
                    [
                        "ot.order_id" => $order['id']
                    ]
                ) ?: [];

                // Group toppings by product_id + size_id
                $toppingsGrouped = [];
                foreach ($toppingsAll as $top) {
                    $gk = $top['product_id'] . '_' . $top['size_id'];
                    $toppingsGrouped[$gk][] = $top;
                }

                // 4️⃣ Fetch ALL removed ingredients for this order (ONCE)
                $removedAll = Database::joinTables(
                    "$order_removed_ingredients_tbl ori",
                    [],
                    ["ori.*"],
                    [
                        "ori.order_id" => $order['id']
                    ]
                ) ?: [];

                // Group removed ingredients by product_id + size_id
                $removedGrouped = [];
                foreach ($removedAll as $ri) {
                    $gk = $ri['product_id'] . '_' . $ri['size_id'];
                    $removedGrouped[$gk][] = $ri;
                }

                // 5️⃣ Attach toppings & removed ingredients to each item
                foreach ($items as $i => $item) {
                    $gk = $item['product_id'] . '_' . $item['size_id'];

                    $items[$i]['toppings'] = $toppingsGrouped[$gk] ?? [];
                    $items[$i]['removed_ingredients'] = $removedGrouped[$gk] ?? [];
                }

                // 6️⃣ Attach items back to order
                $orders[$key]['items'] = $items;
            }

            return $orders;

        } catch (\Throwable $th) {
            Utility::log(
                $th->getMessage(),
                'error',
                'OrderService::fetchAllOrders',
                [],
                $th
            );
            Response::error(500, "An error occurred while fetching orders");
        }
    }

    public static function createNewOrder($orderData){
        try {
            Database::beginTransaction();
                $newOrderId = self::saveOrderInformation($orderData);
                self::processCartItem($orderData, $newOrderId);
                self::processOrderPayment($orderData, $newOrderId);
                self::orderNotifications($orderData);
            Database::commit();
            return $newOrderId;
        } catch (\Throwable $th) {            
            Database::rollBack();
            Utility::log($th->getMessage(), 'error', 'OrderService::createNewOrder', ['Order' => json_encode($orderData)], $th);
            Response::error(500, "An error occurred while creating a new order");
        }
    }

    private static function processCartItem($orderData,$newOrderId){
        foreach($orderData['cart'] as $item){

            if ($item['type'] === 'half_half'){
                self::processHalfHalfItem($item, $newOrderId);
            } else {
                self::processNormalOrder($item, $newOrderId);
            }
            self::processToppingsInfo($newOrderId);
            self::processRemovedIngredients($item, $newOrderId);
           
        }
    }

    private static function saveOrderInformation($orderData){
        try {
        $order = Utility::$orders;
        $status = '';

        isset($orderData['customer_type']) && $orderData['customer_type'] === 'walk_in' ? $status = 'delivered' : $status = 'pending';

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
            return $newOrderId = Database::insert($order, $orderUpload);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    private static function processNormalOrder($item, $newOrderId){
        $order_items = Utility::$order_items;
            $itemData = [
                'order_id' => intval($newOrderId),
                'product_id' => intval($item['id']),
                'barbecue_sauce' => $item['barbecueSauce'] ?? null,
                'size_id' => intval($item['size_id']),
                'unit_price' => floatval($item['price']),
                'qty' => intval($item['qty']),
                'subtotal' => floatval($item['price']) * intval($item['qty']),
            ];
            Database::insert($order_items, $itemData);

            self::handleStockUpdates($item, $newOrderId);
    }

    private static function processHalfHalfItem($item, $newOrderId){
        
        $order_items = Utility::$order_items;

        $productIds = self::splitProductIds($item);

        for ($i = 0; $i <= 2; $i++) {
            $halfPrice = self::processhalfPrice($productIds[$i], $item['size_id']);
            $qty = floatval($item['qty']) / 2; //divide qty by 2 for half pizza

            $halfItem = [
                'order_id' => intval($newOrderId),
                'product_id' => intval(trim($productIds[$i])),
                'barbecue_sauce' => $item['barbecueSauce'] ?? null,
                'size_id' => intval($item['size_id']),
                'unit_price' => floatval($halfPrice),
                'qty' => $qty ?? 0.5,
                'subtotal' => floatval($halfPrice),
            ];

            Database::insert($order_items, $halfItem);       

            //handle stock update
            self::handleStockUpdates([
                'id' => intval(trim($productIds[$i])),
                'size_id' => intval($item['size_id']),
                'qty' => $qty,
            ], $newOrderId);
           
        }
    }

    public static function splitProductIds($item){
        //size is "6,4" and needs to be split into two ids
        return   explode(',', $item['id']);
       
    }

    private static function processhalfPrice($productId, $size){
        //get the actuall product price and divide by 2 for half pizza. This is to ensure that the price is correct even if the frontend sends the full price for both halves
        $productInfo = ProductService::fetchFullProduct($productId);
        $sizes = $productInfo['sizes'];

        foreach ($sizes as $s) {
            if ($s['size_id'] == $size) {
                return floatval($s['price']) / 2; //divide by 2 for half pizza
            }
        }


    }

    private static function processToppingsInfo($newOrderId){
        $order_toppings = Utility::$order_toppings;

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
    }

    private static function processRemovedIngredients($item, $newOrderId){
         if (isset($item['removed_ingredients']) && is_array($item['removed_ingredients'])){
            foreach ($item['removed_ingredients'] as $ingredient){
                Database::insert(Utility::$order_removed_ingredients, [
                    'order_id' => intval($newOrderId),
                    'product_id' => intval($item['id']),
                    'ingredient_name' => $ingredient['ingredient_name'],
                    'size_id' => intval($item['size_id']),
                ]);
            }
        }
    }

    private static function handleStockUpdates($item, $newOrderId){
         $status = ProductStockService::reduceAuto(
                    $item['id'],
                    $item['size_id'],
                    $item['qty']
                );

        if ($status === "insufficient_stock") {
            Utility::log("Insufficient stock for product {$item['id']}", 'error', 'OrderService::handleStockUpdates', ['OrderID' => $newOrderId, 'ProductID' => $item['id']]);
        }               
    }

    private static function processOrderPayment($orderData, $newOrderId){
        $payment = $orderData['payment'];  
        $payments = Utility::$payments;      
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
            'vat' => floatval($payment['vat'] ?? 0),
            'discount' => floatval($payment['discount'] ?? 0),
            'payment_date' => date('Y-m-d H:i:s'),
        ];
            
        Database::insert($payments, $paymentData);
    }

    private static function orderNotifications($orderData){
        if ($orderData['customer_type'] !== 'walk_in'){
            
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
                'delivery_type' => $orderData['delivery_type'] ?? null,
                'delivery_address' => isset($orderData['delivery_address']) ? $orderData['delivery_address'].", ".$orderData['city'] : null,
                'delivery_instructions' => $orderData['order_note'] ?? null,
                'total_amount' => $orderData['total_amount'] ?? 0,
                'order_details' => json_encode($orderData['cart']),
            ]);
            
        }
            
        ActivityService::saveActivity([
            'userid' => $orderData['userid'] ?? $_SESSION['userid'] ?? null,
            'type' => 'order',
            'title' => 'Order created successfully',
        ]);
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
    
    public static function pendingOrders(){
        
    }

      
    
}
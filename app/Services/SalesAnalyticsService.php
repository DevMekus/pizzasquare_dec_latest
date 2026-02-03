<?php
namespace App\Services;
use App\Services\ProductService;
use App\Services\ProductSizesService;


class SalesAnalyticsService{

    public static function calculateTopDishes(array $orders, int $limit = 5){
        $dishSales = [];

        foreach ($orders as $order){
            if (!isset($order['items'])) continue;

            foreach ($order['items'] as $item){
                $dishId = $item['product_id'];

                if (!isset($dishSales[$dishId])){
                    $dishSales[$dishId] = [
                        'product_id' => $dishId,
                        'name' => $item['product_name'] ?? 'unknown',
                        'image_url' => $item['image'] ?? null,
                        'total_qty' => 0,

                    ];
                }

                $dishSales[$dishId]['total_qty'] += (int) $item['qty'];
            }
        }

        usort($dishSales, fn($a, $b) =>$b['total_qty'] <=> $a['total_qty'] );

    

        $limitedProducts =  array_slice($dishSales, 0, $limit);

        $productsWithDetails = [];

        foreach ($limitedProducts as &$product){
            $productDetails = ProductService::fetchById($product['product_id']);
            $sizes = ProductSizesService::fetchByProductId($product['product_id']);
            if ($productDetails){
                $product['details'] = $productDetails;
                $product['sizes'] = $sizes;
                $productsWithDetails[] = $product;
            }
        }

        return $productsWithDetails;
    }

}
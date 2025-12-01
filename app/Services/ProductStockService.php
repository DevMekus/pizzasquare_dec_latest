<?php

namespace App\Services;

use configs\Database;
use App\Utils\Utility;

class ProductStockService
{
    private static string $table = "product_stock";
    private static string $category_stock_table = "category_size_stock";

    /**
     * Fetch product stock by category 
     * Used by UI: /product-stock/{categoryId}
     */
    public static function getProductStock($product_id)
    {
       try {
            $product_stock = 'product_stock';
            $products = Utility::$products;
            
            return Database::joinTables(
                "$product_stock ps",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$products p",
                        "on"   => "ps.product_id = p.id"
                    ],
                   
                ],
                [
                    "ps.*",                   
                    "p.name AS product_name",
                ],
                [
                    "OR" => [
                        "ps.product_id" => $product_id,
                    ]
                ],
                [                   
                    "order" => "ps.size_id ASC"
                ]
            );

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductService::fetchById', [], $th);
            return false;
        }
    } 

     public static function getProductStocks()
    {
       try {
            $product_stock = 'product_stock';
            $products = Utility::$products;
            $sizes = Utility::$sizes;   
            
            return Database::joinTables(
                "$product_stock ps",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$products p",
                        "on"   => "ps.product_id = p.id"
                    ],
                    [
                        "type" => "LEFT",
                        "table" => "$sizes s",
                        "on"   => "ps.size_id = s.id"
                    ],
                   
                ],
                [
                    "ps.*",                   
                    "p.name AS product_name",
                    "s.label AS size_label"
                ],
                [                   
                ],
                [                   
                    "order" => "ps.size_id ASC"
                ]
            );

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductService::fetchById', [], $th);
            return false;
        }
    } 
    

    public static function getCategoryStock($category_id)
    {
       try {
            $category_stock = Utility::$category_size_stock;
            $categories = Utility::$categories;
            $sizes = Utility::$sizes;   
            
            return Database::joinTables(
                "$category_stock cp",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$categories c",
                        "on"   => "cp.category_id = c.id"
                    ],                    
                    [
                        "type" => "LEFT",
                        "table" => "$sizes s",
                        "on"   => "cp.size_id = s.id"
                    ],
                   
                ],
                [
                    "cp.*",                   
                    "c.name AS category",
                    "s.label AS size"
                ],
                [
                    "OR" => [
                        "cp.category_id" => $category_id,
                    ]
                ],
                [                   
                    "order" => "cp.size_id ASC"
                ]
            );

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductService::fetchById', [], $th);
            return false;
        }
    } 
    /**
     * Get current stock for a product (optionally by size)
     */
    

    public static function getStock($product_id, $size_id = null)
    {
        try {
            $productstock_tbl = self::$table;       
            $products_tbl     = Utility::$products; 

            // Build WHERE conditions
            $where = ["ps.product_id" => $product_id];
            if ($size_id) {
                $where["ps.size_id"] = $size_id;
            }

            return Database::joinTables(
                "$productstock_tbl ps",   
                [
                    [
                        "type"  => "LEFT",
                        "table" => "$products_tbl p",
                        "on"    => "p.id = ps.product_id"
                    ]
                ],
                [
                    "ps.*",
                    "p.name AS product_name",
                    "p.sku AS product_sku",
                    "p.image AS product_image",
                    "p.category_id"
                ],
                $where,
                [
                    "order" => "ps.id ASC"
                ]
            );

        } catch (\Throwable $th) {
            Utility::log(
                $th->getMessage(),
                'error',
                'ProductStockService::getStock',
                ['product_id'=>$product_id, 'size_id'=>$size_id],
                $th
            );
            return false;
        }
    }

    /**
     * Update product stock (qty + low stock)
     * Used by UI: /api/product-stock/{stock_id}/update
     */
    public static function adjustProductStock(int $stock_id, array $data)
    {
        try {
            $updateData = [];

            if (isset($data['qty'])) {
                $updateData['qty'] = intval($data['qty']);
            }

            if (isset($data['low_stock_threshold'])) {
                $updateData['low_stock_threshold'] = intval($data['low_stock_threshold']);
            }

            if (!$updateData) return false;

            return Database::update(self::$table, $updateData, ['id' => $stock_id]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), "error", "ProductStockService::adjustProductStock", ['id'=>$stock_id], $th);
            return false;
        }
    }

     /**
     * Update category stock (qty + low stock)
     * Used by UI: /api/category-stock/{stock_id}/update
     */
    public static function adjustCategoryStock(int $stock_id, array $data)
    {
        try {
            $updateData = [];

            if (isset($data['qty'])) {
                $updateData['qty'] = intval($data['qty']);
            }

            if (isset($data['low_stock_threshold'])) {
                $updateData['low_stock_threshold'] = intval($data['low_stock_threshold']);
            }

            if (!$updateData) return false;

            return Database::update(self::$category_stock_table, $updateData, ['id' => $stock_id]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), "error", "ProductStockService::adjustCategoryStock", ['id'=>$stock_id], $th);
            return false;
        }
    }

    /**
     * DECREASE stock after order
     */
    public static function reduceStock($product_id, $size_id, $qty)
    {
        try {
            $row = Database::findWhere(self::$table, [
                "product_id" => $product_id,
                "size_id"    => $size_id
            ]);

            if (!$row) return false;

            if ($row["qty"] < $qty) {
                return "insufficient_stock";
            }

            $newQty = $row["qty"] - $qty;

            return Database::update(self::$table, ["qty" => $newQty], ["id" => $row["id"]]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductStockService::reduceStock', compact('product_id','size_id','qty'), $th);
            return false;
        }
    }
}

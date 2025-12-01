<?php

namespace App\Services;

use configs\Database;
use App\Utils\Utility;

class ProductSizesService
{
    private string $table = "product_sizes";

    /**
     * Get all sizes for a product
     */
    

    public static function fetchByProductId($productId)
    {       

        return Database::joinTables(
            "product_sizes ps",
            [
                [
                    "type" => "LEFT",
                    "table" => "sizes s",
                    "on" => "ps.size_id = s.id"
                ],
                [
                    "type" => "LEFT",
                    "table" => "product_stock psck",
                    "on" => "ps.product_id = psck.product_id AND ps.size_id = psck.size_id"
                ],
                [
                    "type" => "LEFT",
                    "table" => "category_size_stock csck",
                    "on" => "csck.size_id = ps.size_id AND csck.category_id = (
                        SELECT p.category_id FROM products p WHERE p.id = ps.product_id LIMIT 1
                    )"
                ],
            ],
            [
                "ps.id",
                "ps.price",
                "ps.shared_stock",
                "psck.qty AS product_stock_quantity",
                "csck.qty AS category_stock_quantity",
                "s.label AS size_label",
                "s.code AS size_code",
                "s.id AS size_id"
            ],
            [
                "ps.product_id" => $productId
            ],
            [
                "order" => "s.ordering ASC"
            ]
        );
    }

    /**
     * Add new size for a product
     */
    public static function addBulkSizes($data)
    {
        try {

            foreach ($data as $product) {

                $upload = [
                    "product_id"        => intval($product['product_id']),
                    "size_id"           => intval($product['size_id']),
                    "price"             => intval($product['price']),
                    "shared_stock" => intval($product['shared_stock']),
                ];

                Database::insert("product_sizes", $upload);
                
                //if $product['shared_stock'] = 0 then save product in product_stock with 0 qty
                if (isset($product['shared_stock']) && intval($product['shared_stock']) === 0) {
                    Database::insert("product_stock", [
                        "product_id" => intval($product['product_id']),
                        "size_id"     => intval($product['size_id']), 
                        "qty"         => 0
                    ]); 
                } else {
                    // Else, save the shared stock quantity
                    //Get existing category_size_stock entry and be sure not to duplicate
                    $existingEntry = Database::findWhere("category_size_stock", [
                        "category_id" => intval($product['category_id']),
                        "size_id"     => intval($product['size_id'])
                    ]);


                    if (!$existingEntry) {
                        Database::insert("category_size_stock", [
                            "category_id" => intval($product['category_id']),
                            "size_id"     => intval($product['size_id']), 
                            "qty"         => 0
                        ]);
                    }   
                }            
               
                
                
                        
            }

            return true;

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductSizesService::addBulkSizes', $data, $th);
            return false;
        }
    }


    /**
     * Update a product size
     */
    public static function updateSize($id, $data)
    {
        try {
            $updateData = [];
            if (isset($data['size'])) $updateData['size'] = $data['size'];
            if (isset($data['is_active'])) $updateData['is_active'] = $data['is_active'];

            return Database::update("product_sizes", $updateData, ["id" => $id]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductSizesService::updateSize', ['id'=>$id,'data'=>$data], $th);
            return false;
        }
    }

    /**
     * Delete a product size
     */
    public static function deleteSize($id)
    {
        try {
            return Database::delete("product_sizes", ["id" => $id]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductSizesService::deleteSize', ['id'=>$id], $th);
            return false;
        }
    }
}

<?php

namespace App\Services;

use configs\Database;
use App\Utils\Utility;

class CategorySizeStockService
{
    
    public static function fetchAll() {
        $category_stock = Utility::$category_size_stock;
        $categories = Utility::$categories; 
        $sizes = Utility::$sizes; 

        try {
            return Database::joinTables(
                "$categories c",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$sizes s",
                        "on"   => "s.category_id = c.id"
                    ],
                    [
                        "type" => "LEFT",
                        "table" => "$category_stock cs",
                        "on"   => "cs.size_id = s.id AND cs.category_id = c.id"
                    ],
                ],
                [
                    "c.id",
                    "c.name AS category",

                    "CASE 
                        WHEN COUNT(s.id) = 0 THEN JSON_ARRAY()
                        ELSE JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', s.id,
                                'label', s.label,
                                'code', s.code,
                                'ordering', s.ordering,
                                'qty', cs.qty,
                                'low_stock_threshold', cs.low_stock_threshold
                            )
                        )
                    END AS sizes"
                ],
                [],
                [
                    "group" => "c.id",
                    "order" => "c.name ASC"
                ]
            );

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategorySizeStockService::fetchAll', [], $th);
            return false;
        }
    }
     
    public static function fetchById($id) {
        $category_stock = Utility::$category_size_stock;
        $categories = Utility::$categories; 
        $sizes = Utility::$sizes; 

        try {
            return Database::joinTables(
                "$categories c",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$sizes s",
                        "on"   => "s.category_id = c.id"
                    ],
                    [
                        "type" => "LEFT",
                        "table" => "$category_stock cs",
                        "on"   => "cs.size_id = s.id AND cs.category_id = c.id"
                    ],
                ],
                [
                    "c.id",
                    "c.name AS category",

                    "CASE 
                        WHEN COUNT(s.id) = 0 THEN JSON_ARRAY()
                        ELSE JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', s.id,
                                'label', s.label,
                                'code', s.code,
                                'ordering', s.ordering,
                                'qty', cs.qty,
                                'low_stock_threshold', cs.low_stock_threshold
                            )
                        )
                    END AS sizes"
                ],
                [
                    "OR" => [
                       "c.id" => $id,
                    ]
                ],
                [
                    "group" => "c.id",
                    "order" => "c.name ASC"
                ]
            );

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategorySizeStockService::fetchAll', [], $th);
            return false;
        }
    }
    public static function create($data){
        try {
            
          
            if (!isset($data['category_id']) || !isset($data['size_id']) || !isset($data['stock'])) {
                return false;
            }
 
            $category_size_stock = [
                "category_id" => intval($data['category_id']),
                "size_id"     => intval($data['size_id']),
                "qty"       => intval($data['qty']),
                "low_stock_threshold"       => isset($data['low_stock_threshold']) ? intval($data['low_stock_threshold']) : 5,

            ];
            return Database::insert(Utility::$category_size_stock, $category_size_stock);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategorySizeStockService::create', [], $th);
            return false;
        }
    }

    public static function update($id, $data){
        try {
            if (!isset($data['category_id']) || !isset($data['size_id']) || !isset($data['stock'])) {
                return false;
            }

            $category_size_stock = [
                "category_id" => intval($data['category_id']),
                "size_id"     => intval($data['size_id']),
                "qty"       => intval($data['qty']),
                "low_stock_threshold"       => isset($data['low_stock_threshold']) ? intval($data['low_stock_threshold']) : 5,
            ];
            return Database::update(Utility::$category_size_stock, $category_size_stock, ["id" => intval($id)]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategorySizeStockService::update', [], $th);
            return false;
        }
    }

    public static function delete($id){
        try {
            return Database::delete(Utility::$category_size_stock, ["id" => intval($id)]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategorySizeStockService::delete', [], $th);
            return false;
        }
    }


}
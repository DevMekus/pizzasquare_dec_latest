<?php

namespace App\Services;

use App\Utils\Response;
use configs\Database;
use App\Utils\Utility;

class SizesService
{
    private static string $table = "sizes";
    private static string $categories = "categories";

    /**
     * Fetch all sizes with category join
     */
    public static function fetchAll()
    {
        $sizes = Utility::$sizes;
        $categories = Utility::$categories; 

         try {
            return Database::joinTables(
                "$sizes sizes",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$categories categories",
                        "on"   => "sizes.category_id = categories.id"
                    ],
                ],
                [
                    "sizes.*",
                    "categories.name AS category"                  
                ],
                [],
                [
                      "order" => "categories.name ASC, sizes.ordering ASC"
                ]
            );
        } catch (\Throwable $th) {
             Utility::log($th->getMessage(), 'error', 'SizesService::fetchAll', [], $th);
            return false;
        }
    }

    /**
     * Fetch size by ID
     */
    public static function fetchById($id)
    {
        $sizes = Utility::$sizes;
        $categories = Utility::$categories; 

         try {
            return Database::joinTables(
                "$sizes sizes",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$categories categories",
                        "on"   => "sizes.category_id = categories.id"
                    ],
                ],
                [
                    "sizes.*",
                    "categories.name AS category"                  
                ],
                [
                     "OR" => [
                       "sizes.id" => $id,
                       "sizes.category_id" => $id,
                    ]
                    
                ],
                [
                    "order" => "sizes.ordering ASC"
                ]
            );
        } catch (\Throwable $th) {
             Utility::log($th->getMessage(), 'error', 'SizesService::fetchAll', [], $th);
            return false;
        }
    }

    /**
     * Create new size
     */
    public static function create($data)
    {
        try {
            if (!isset($data['label']) || !isset($data['category_id']) || !isset($data['ordering'])) {
                return false;
            }

            $size = [
                "category_id" => intval($data['category_id']),
                "code"        => $data['code'] ?? Utility::generate_uniqueId(),
                "label"       => $data['label'],
                "ordering"    => intval($data['ordering']),
            ];

            return Database::insert(self::$table, $size);

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'SizesService::create', $data, $th);
            return false;
        }
    }

    /**
     * Update size
     */
    public static function update($id, $data, $prev)
    {
        try {
            $updateData = [];

            $updateData = [
                "category_id" => isset($data['category_id']) ? intval($data['category_id']) : $prev['category_id'],
                "code"        => isset($data['code']) ? $data['code'] : $prev['code'],
                "label"       => isset($data['label']) ? $data['label'] : $prev['label'],
                "ordering"    => isset($data['ordering']) ? intval($data['ordering']) : $prev['ordering'],
            ];          

            return Database::update(self::$table, $updateData, ['id'=>$id]);

        } catch (\Throwable $th) {
            Utility::log(
                $th->getMessage(),
                'error',
                'SizesService::update',
                ['id'=>$id, 'data'=>$data],
                $th
            );
            return false;
        }
    }

    /**
     * Delete size
     */
    public static function delete($id)
    {
        try {
            return Database::delete(self::$table, ['id' => $id]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'SizesService::delete', ['id'=>$id], $th);
            return false;
        }
    }
}

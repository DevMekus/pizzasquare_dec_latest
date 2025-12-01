<?php

namespace App\Services;

use App\Utils\Utility;
use configs\Database;
use App\Utils\Response;

class ExtrasService
{

    public static function fetchExtras($id)
    {
        $extras_tbl = Utility::$extras;
        $category_tbl = Utility::$categories;
        try {
            return Database::joinTables(
                "$extras_tbl extras",
                [
                    [
                        "type"  => "LEFT",
                        "table" => "$category_tbl category",
                        "on"    => "extras.category_id  = category.id"
                    ],
                ],
                ["extras.*", "category.name"],
                [
                    "OR" => [
                        "extras.id" => $id,
                        "extras.extras" => $id,
                    ]
                ],
                ["extras.id" => $id]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ExtrasService::fetchExtras', ['extras' => $id], $th);
            Response::error(500, "An error occurred while fetching a extras");
        }
    }


    public static function fetchAllExtras()
    {
        $extras_tbl = Utility::$extras;
        $category_tbl = Utility::$categories;
        try {
            return Database::joinTables(
                "$extras_tbl extras",
                [
                    [
                        "type"  => "LEFT",
                        "table" => "$category_tbl category",
                        "on"    => "extras.category_id  = category.id"
                    ],
                ],
                ["extras.*", "category.name"],
                [],
                []
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ExtrasService::fetchExtras', ['extras' => ''], $th);
            Response::error(500, "An error occurred while fetching all extras");
        }
    }


    public static function createExtras($data)
    {
        try {
            $extras_tbl = Utility::$extras;

            $extras = [
                'extras' => $data['extras'],
                'extras_price' => intval($data['extras_price']) ?? 0,
                'category_id' => intval($data['category_id']),
            ];

            if (Database::insert($extras_tbl, $extras)) {

                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'extras',
                    'title' => 'new extras added',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ExtrasService::createNewExtra', ['category' => ''], $th);
            Response::error(500, "An error occurred while creating extra");
        }
    }

    public static function updateExtras($id, $data)
    {
        try {
            $extras_tbl = Utility::$extras;
            $existing = self::fetchExtras($id);

            if (empty($existing)) {
                Response::error(404, "extras not found");
            }
            $extras = $existing[0];

            $update = [
                'extras' => isset($data['extras']) ? $data['extras'] : $extras['extras'],
                'extras_price' => isset($data['extras_price']) ? intval($data['extras_price']) : intval($extras['extras_price']),
                'category_id' => isset($data['category_id']) ? intval($data['category_id']) : intval($extras['category_id']),
            ];

            if (Database::update($extras_tbl, $update, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'extras',
                    'title' => 'Extras updated',
                ]);
                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ExtrasService::updateExtras', ['category' => ''], $th);
            Response::error(500, "An error occurred while updating extras");
        }
    }

    public static function deleteExtras($id)
    {
        try {
            $extras_tbl = Utility::$extras;

            if (Database::delete($extras_tbl, ['id' => $id])) {

                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'extras',
                    'title' => 'Extras deleted',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ExtrasService::deleteExtras', ['category' => ''], $th);
            Response::error(500, "An error occurred while deleting extras");
        }
    }
}

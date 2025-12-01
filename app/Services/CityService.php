<?php

namespace App\Services;

use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;

class CityService
{


    public static function fetchACity($id)
    {
        $city = Utility::$city;
        try {
            return Database::joinTables(
                "$city c",
                [],
                ["c.*"],
                [
                    "OR" => [
                        "c.id" => $id,
                        "c.city" => $id,
                    ]
                ],
                ["c.id" => $id]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CityService::fetchACity', ['city' => $id], $th);
            Response::error(500, "An error occurred while fetching a city");
        }
    }

    public static function fetchAllCities()
    {
        $city = Utility::$city;
        try {
            return Database::joinTables(
                "$city c",
                [],
                ["c.*"],

            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CityService::fetchACity', ['city' => ''], $th);
            Response::error(500, "An error occurred while fetching all cities");
        }
    }

    public static function createNewCity($data)
    {
        try {
            $city_tbl = Utility::$city;
            $existing = Database::find($city_tbl, $data['city'], 'city');

            if ($existing) {
                Response::error(409, "city already exists");
            }

            $city = [
                'city' => $data['city'],
                'delivery_price' => intval($data['delivery_price']),
            ];

            if (Database::insert($city_tbl, $city)) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'delivery',
                    'title' => 'delivery city added',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryService::createNewCity', ['category' => ''], $th);
            Response::error(500, "An error occurred while creating city");
        }
    }

    public static function updateCity($id, $data)
    {
        try {
            $city_tbl = Utility::$city;
            $existing = self::fetchACity($id);

            if (empty($existing)) {
                Response::error(404, "city not found");
            }

            $city = $existing[0];

            $update = [
                'city' => isset($data['city']) ? $data['city'] : $city['city'],
                'delivery_price' => isset($data['delivery_price']) ? intval($data['delivery_price']) : intval($city['delivery_price']),
            ];

            if (Database::update($city_tbl, $update, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'delivery',
                    'title' => 'delivery city updated',
                ]);
                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CityService::updateCity', ['category' => ''], $th);
            Response::error(500, "An error occurred while updating city");
        }
    }

    public static function deleteCity($id)
    {
        try {
            $city_tbl = Utility::$city;
            $existing = self::fetchACity($id);

            if (empty($existing)) {
                Response::error(404, "city not found");
            }


            if (Database::delete($city_tbl, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'delivery',
                    'title' => 'delivery city deleted',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CityService::deleteCity', ['city' => ''], $th);
            Response::error(500, "An error occurred while updating category");
        }
    }
}

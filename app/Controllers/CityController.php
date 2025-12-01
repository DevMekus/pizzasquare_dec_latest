<?php

namespace App\Controllers;

use App\Services\CityService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class CityController
{

    public function listCities()
    {
        try {
            $cities = CityService::fetchAllCities();

            if (empty($cities))
                Response::error(404, "cities not found");

            Response::success($cities, "cities found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CityController::listCities', [], $e);
            Response::error(500, "Error fetching cities");
        }
    }

    public function fetchCityById($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $city = CityService::fetchACity($id);

            if (empty($city))
                Response::error(404, "city not found");

            Response::success($city, "city found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CityController::fetchCityById', [], $e);
            Response::error(500, "Error fetching city");
        }
    }

    public function postCity()
    {
        try {
            $data = RequestValidator::validate([
                'city' => 'required|string',
                'delivery_price' => 'required|int',
            ]);

            $city = CityService::fetchACity($data['city']);

            if (!empty($city))
                Response::error(409, "city already exist");

            $data = RequestValidator::sanitize($data);

            if (CityService::createNewCity($data))
                Response::success([], "city saved");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CityController::postACity', [], $e);
            Response::error(500, "Error posting city");
        }
    }

    public function updateCity($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $data = RequestValidator::validate([
                'city' => 'required|string',
                'delivery_price' => 'required|int',
            ]);

            $city = CityService::fetchACity($id);

            if (empty($city))
                Response::error(404, "city not found");

            if (CityService::updateCity($id, $data))
                Response::success([], "city saved");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CityController::updateCity', [], $e);
            Response::error(500, "Error updating city");
        }
    }

    public function deleteCity($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $city = CityService::fetchACity($id);

            if (empty($city))
                Response::error(404, "city not found");

            if (CityService::deleteCity($id))
                Response::success([], "city deleted");

        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CityController::deleteCity', [], $e);
            Response::error(500, "Error deleting city");
        }
    }
}

<?php

namespace App\Controllers;

use App\Services\CityService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class Geocode
{
    public function reverseGeocode()
    {

        try {
            $data = RequestValidator::validate([
                'lat'     => 'require|min:3',
                'lon' => 'required|address',
            ]);

            $data = RequestValidator::sanitize($data);

            if (!isset($data['lat']) || !isset($data['lon'])) {
                http_response_code(400);
                echo json_encode(["error" => "Latitude and longitude required"]);
                exit;
            }


            $lat = $data['lat'];
            $lon = $data['lon'];



            // Fetch from Nominatim
            $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$lat}&lon={$lon}";

            // Set a custom User-Agent (Nominatim requires this)
            $options = [
                "http" => [
                    "header" => "User-Agent: Pizzasquare/1.0 (info@https://pizzasquare.ng/)\r\n"
                ]
            ];
            $context = stream_context_create($options);

            $response = file_get_contents($url, false, $context);

            if ($response === FALSE) {
                http_response_code(500);
                echo json_encode(["error" => "Failed to fetch location"]);
                exit;
            }

            $data = json_decode($response, true);

            $deliveryZones = CityService::fetchAllCities();

            if (empty($deliveryZones))
                Response::error(404, "cities not found");

            // ---Match with delivery zones ---
            $area = $data["address"]["suburb"]
                ?? $data["address"]["city_district"]
                ?? $data["address"]["city"]
                ?? null;

            $matchedPrice = null;
            $area = is_string($area) ? trim($area) : '';

            foreach ($deliveryZones as $zone) {
                $zoneName = (string)$zone['city'];
                $price    = (int)$zone['delivery_price'];

                if ($area !== '' && stripos($area, $zoneName) !== false) {
                    $matchedPrice = $price;
                    break;
                }
            }





            $response = [
                "lat" => $lat,
                "lon" => $lon,
                "area" => $area,
                "delivery_fee" => $matchedPrice,
                "raw" => $data
            ];

            Response::success($response, "Location detected");

            // // Return JSON response
            // header("Content-Type: application/json");
            // echo json_encode([
            //     "lat" => $lat,
            //     "lon" => $lon,
            //     "area" => $area,
            //     "delivery_fee" => $matchedPrice,
            //     "raw" => $data
            // ]);
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'Geocode::reverseGeocode', [], $e);
            Response::error(500, "Error fetching location");
        }
    }
}

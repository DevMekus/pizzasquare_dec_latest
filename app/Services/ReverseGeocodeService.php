<?php

namespace App\Services;

use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;
use App\Services\CityService;

class ReverseGeocodeService{

    public static function getLocation($lon, $lat){
        try {
            $table = Utility::$geolocation;

            $query = Database::findWhere($table, ['longitude' => $lon, 'latitude' => $lat]);
            return $query;
        } catch (\Throwable $th) {
            Utility::log("Error fetching location: " . $th->getMessage());
            return false;            
        }
    }

    public static function reverseGeocode($lat, $lng){
        try {
            //LocationIQ
            $apiKey = "pk.c6f25da0b4ca284e53a2c6b85660a0c7";

            $url = "https://us1.locationiq.com/v1/reverse.php?key={$apiKey}&lat={$lat}&lon={$lng}&format=json";

            $request = file_get_contents($url);

            if (!$request) {
                Response::error(500, "Failed to connect to geocoding service");               
            }
           
            $result = json_decode($request, true);

            $addressInfo = [
                'longitude' => $lng,
                'latitude' => $lat, 
                'display_name' => $result['display_name'] ?? null,
                'suburb' => $result['address']['suburb'] ?? null,
                'residential' => $result['address']['residential'] ?? null,
                'city' => $result['address']['city'] ?? null,
                'state' => $result['address']['state'] ?? null,
                'county' => $result['address']['county'] ?? null,
                'county' => $result['address']['county'] ?? null,
            ];          

            self::saveNewAddress($addressInfo);

            $mappedInfo = self::mapLocationToResponse($addressInfo);                   

            return $mappedInfo;

        } catch (\Throwable $th) {
            Utility::log("Error in reverseGeocode: " . $th->getMessage());
        }
    }

    public  static function MapSurbubToCity($suburb){
        $deliveryZones = CityService::fetchAllCities();

        if (empty($deliveryZones))
            Response::error(404, "cities not found");

        $matchedCity = null;
        $matchedPrice = null;
        $suburb = is_string($suburb) ? trim(strtolower($suburb)) : '';
         
            foreach ($deliveryZones as $zone) {
                $zoneName = (string)strtolower($zone['city']);
                $price    = (int)$zone['delivery_price'];

                if ($suburb !== '' && stripos($suburb, $zoneName) !== false) {
                    $matchedCity = $zone['city'];
                    $matchedPrice = $price;
                    break;
                }
            }    

        return [
            'city' => $matchedCity,
            'price' => $matchedPrice
        ];
    }

    public static function mapLocationToResponse($location){

       
        $area = $location['suburb'] ?? $location['residential'] ?? null;
        $area = is_string($area) ? trim(strtolower($area)) : '';

        $matchedCityInfo = self::MapSurbubToCity($area);
        $location['delivery_price'] = $matchedCityInfo['price'] ?? null;
         

        return [
            'longitude' => $location['longitude'],
            'latitude' => $location['latitude'], 
            'display_name' => $location['display_name'] ?? null,
            'suburb' => $location['suburb'] ?? null,
            'residential' => $location['residential'] ?? null,
            'city' => $location['city'] ?? null,
            'state' => $location['state'] ?? null,
            'county' => $location['county'] ?? null,
            'delivery_price' => $location['delivery_price'] ?? null
        ];
    }

    public static function saveNewAddress($address){
        try {
            $table = Utility::$geolocation;

            $lat = round($address['latitude'], 5);
            $lng = round($address['longitude'], 5);     

            $query = "INSERT INTO {$table} (longitude, latitude, display_name, suburb, residential, city, state, county) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $lng,
                $lat,
                $address['display_name'],
                $address['suburb'],
                $address['residential'],
                $address['city'],
                $address['state'],
                $address['county']
            ];
            return Database::query($query, $params);
        } catch (\Throwable $th) {
            Utility::log("Error saving location: " . $th->getMessage());
            return false;            
        }
    }
}
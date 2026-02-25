<?php

namespace App\Controllers;

use App\Services\ReverseGeocodeService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class ReverseGeocode{
     public function reverseGeocode(){
        try {
            $data = RequestValidator::validate([
                'lat'     => 'require|min:3',
                'lon' => 'required|min:3',
            ]);

            $data = RequestValidator::sanitize($data);
            $lat = round($data['lat'], 5);
            $lng = round($data['lon'], 5);           

            //Check if the 

            $locationExists = ReverseGeocodeService::getLocation($lng, $lat);
       
            if ($locationExists){
                $surbub = $locationExists['suburb'];
                $currentPrice = ReverseGeocodeService::MapSurbubToCity($surbub);
               
                $mappedInfo = ReverseGeocodeService::mapLocationToResponse($locationExists);
                $mappedInfo['delivery_price'] = $currentPrice['price'];

                Response::success($mappedInfo, "Location found in database");
           

            }

            $reversed = ReverseGeocodeService::reverseGeocode($lat, $lng);
            Response::success($reversed, "Location reversed successfully");
            

        } catch (\Throwable $th) {
            Utility::log("Error in reverse geocoding: " . $th->getMessage(), 'error', 'ReverseGeocode::reverseGeocode', [], $th);
            Response::error(500, "An error occurred while processing the request");
        }
     }
}
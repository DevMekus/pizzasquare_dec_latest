<?php

namespace App\Services;

use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;

class PromotionsService
{

    public static function fetchPromotion($id)
    {
        $promotions_tbl = Utility::$promotions;

        try {
            return Database::joinTables(
                "$promotions_tbl p",
                [],
                ["p.*"],
                [
                    "OR" => [
                        "p.id" => $id, 
                        "p.title" => $id,
                        "p.code" => $id
                    ]
                ],
                ["p.id" => $id]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'PromotionsService::fetchPromotion', ['promotion' => $id], $th);
            Response::error(500, "An error occurred while fetching a promotion");
        }
    }

    public static function fetchAllPromotions()
    {
        $promotions_tbl = Utility::$promotions;

        try {
            return Database::joinTables(
                "$promotions_tbl p",
                [],
                ["p.*"],
                [],                
                [                   
                    "order" => "p.id DESC"
                ]

            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'PromotionsService::fetchAllPromotions', ['promotion' => ''], $th);
            Response::error(500, "An error occurred while fetching all promotions");
        }
    }

    public static function createPromotion($data)
    {
        $promotions_tbl = Utility::$promotions;

        $banner = null;

        if (
                isset($_FILES['promoBanner']) &&
                $_FILES['promoBanner']['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['promoBanner']['tmp_name'])
            ) {
                $target_dir =   "public/UPLOADS/promotions/";
                $promo_banner = Utility::uploadDocuments('promoBanner', $target_dir);
                if (!$promo_banner || !$promo_banner['success']) Response::error(500, "Image upload failed");
                $banner = $promo_banner['files'][0];
            }

            //use the title to form the code by stripping of space and making small letters
            $code = strtolower(str_replace(' ', '_', $data['title']));

        try {
            $promotion = [                
                'code' =>  $code,
                'title' => $data['title'],
                'banner' => $banner,
                'description' => $data['description'],               
                'active_day' => strtolower($data['active_day']),               
                'created_at' => date('y-m-d', time()),
            ];

            $promotion_id = Database::insert($promotions_tbl, $promotion);

            ActivityService::saveActivity(
              [
                'userid' => $_SESSION['userid'],
                'type' => 'promotions',
                'title' => 'new promotion created',
              ]
            );

            return $promotion_id;
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'PromotionsService::createPromotion', ['data' => $data], $th);
            Response::error(500, "An error occurred while creating a promotion");
        }
    }

    public static function updatePromotion($id, $data, $previous)
    {
        $promotions_tbl = Utility::$promotions;

        try {

        if (isset($data['title'])){
            //use the title to form the code by stripping of space and making small letters
            $data['code'] = strtolower(str_replace(' ', '_', $data['title']));
        }


            $promotion = [ 
                'code' => isset($data['title']) ? $data['code'] : $previous['code'],            
                'title' => $data['title'] ?? $previous['title'],            
                'description' => $data['description'] ?? $previous['description'],               
                'status' => strtolower($data['status']) ?? $previous['status'],               
                'active_day' => strtolower($data['active_day']) ?? $previous['active_day'],               
                'created_at' => date('y-m-d', time()),
            ];


            if (
                isset($_FILES['promoBanner']) &&
                $_FILES['promoBanner']['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['promoBanner']['tmp_name'])
            ) {
                $target_dir =   "public/UPLOADS/promotions/";
                $promo_banner = Utility::uploadDocuments('promoBanner', $target_dir);
                if (!$promo_banner || !$promo_banner['success']) Response::error(500, "Image upload failed");


                if (isset($previous['banner'])) {

                    $target_dir = "public/UPLOADS/promotions/";

                    $filenameFromUrl = basename($previous['banner']);
                    $file = "../" . $target_dir  . $filenameFromUrl;
                    if (file_exists($file))
                        unlink($file);
                }

                $promotion['banner'] = $promo_banner['files'][0];
            }

            $updated = Database::update($promotions_tbl, $promotion, ["id" => $id]);

            ActivityService::saveActivity(
              [
                'userid' => $_SESSION['userid'],
                'type' => 'promotions',
                'title' => 'promotion updated',
              ]
            );

            return $updated;
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'PromotionsService::updatePromotion', ['id' => $id, 'data' => $data], $th);
            Response::error(500, "An error occurred while updating the promotion");
        }
    }

    public static function deletePromotion($id, $previous)
    {
        $promotions_tbl = Utility::$promotions;

        try {
            
            if (isset($previous['banner'])) {

                $target_dir = "public/UPLOADS/promotions/";

                $filenameFromUrl = basename($previous['banner']);
                $file = "../" . $target_dir  . $filenameFromUrl;
                if (file_exists($file))
                    unlink($file);
            }

            $deleted = Database::delete($promotions_tbl, ["id" => $id]);

            ActivityService::saveActivity(
              [
                'userid' => $_SESSION['userid'],
                'type' => 'promotions',
                'title' => 'promotion deleted',
              ]
            );

            return $deleted;
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'PromotionsService::deletePromotion', ['id' => $id], $th);
            Response::error(500, "An error occurred while deleting the promotion");
        }
    }
}
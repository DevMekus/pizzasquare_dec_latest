<?php

namespace App\Services;

use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;

class DealService
{

    public static function fetchADeal($id)
    {
        $deal_tbl = Utility::$deals;

        try {
            return Database::joinTables(
                "$deal_tbl d",
                [],
                ["d.*"],
                [
                    "OR" => [
                        "d.id" => $id,
                        "d.deal_id" => $id,

                    ]
                ],
                ["d.id" => $id]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'DealService::fetchADeal', ['deal' => $id], $th);
            Response::error(500, "An error occurred while fetching a deal");
        }
    }


    public static function fetchAllDeals()
    {
        $deal_tbl = Utility::$deals;

        try {
            return Database::joinTables(
                "$deal_tbl d",
                [],
                ["d.*"],
                [],                
                [                   
                    "order" => "d.id DESC"
                ]

            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'DealService::fetchAllDeals', ['deal' => ''], $th);
            Response::error(500, "An error occurred while fetching all deal");
        }
    }


    public static function createNewDeal($data)
    {
        try {
            $deal_tbl = Utility::$deals;

            $deal = [
                'deal_id' => Utility::generate_uniqueId(),               
                'title' => $data['title'],
                'description' => $data['description'],               
                'created_at' => date('y-m-d', time()),
            ];

            if (
                isset($_FILES['dealsBanner']) &&
                $_FILES['dealsBanner']['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['dealsBanner']['tmp_name'])
            ) {
                $target_dir =   "public/UPLOADS/deals/";
                $deal_banner = Utility::uploadDocuments('dealsBanner', $target_dir);
                if (!$deal_banner || !$deal_banner['success']) Response::error(500, "Image upload failed");

                $deal['image'] = $deal_banner['files'][0];
            }

            if (Database::insert($deal_tbl, $deal)) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'deals',
                    'title' => 'new deal created',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'DealService::createNewDeal', ['deal' => ''], $th);
            Response::error(500, "An error occurred while creating a deal");
        }
    }

    public static function updateADeal($id, $data, $deal)
    {
        try {
            $deal_tbl = Utility::$deals;


            $update = [
              
                'title' => isset($data['title']) ? $data['title'] : $deal['title'],
                'description' => isset($data['description']) ? $data['description'] : $deal['description'],                
                'status' => isset($data['status']) ? $data['status'] : $deal['status'],
            ];

            if (
                isset($_FILES['dealsBanner']) &&
                $_FILES['dealsBanner']['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['dealsBanner']['tmp_name'])
            ) {
                $target_dir =   "public/UPLOADS/deals/";
                $deal_banner = Utility::uploadDocuments('dealsBanner', $target_dir);
                if (!$deal_banner || !$deal_banner['success']) Response::error(500, "Image upload failed");


                if (isset($deal['image'])) {

                    $target_dir = "public/UPLOADS/deals/";

                    $filenameFromUrl = basename($deal['image']);
                    $file = "../" . $target_dir  . $filenameFromUrl;
                    if (file_exists($file))
                        unlink($file);
                }

                $update['image'] = $deal_banner['files'][0];
            }

            if (Database::update($deal_tbl, $update, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'deal',
                    'title' => 'deal updated',
                ]);
                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'DealService::updateADeal', ['deal' => $id], $th);
            Response::error(500, "An error occurred while updating deal");
        }
    }

    public static function deleteADeal($id)
    {
        try {
            $deal_tbl = Utility::$deals;
            $existing = self::fetchADeal($id);

            if (empty($existing)) {
                Response::error(404, "deal not found");
            }

            $deal = $existing[0];

            if (isset($deal['image'])) {

                $target_dir = "public/UPLOADS/deals/";

                $filenameFromUrl = basename($deal['image']);
                $file = "../" . $target_dir  . $filenameFromUrl;
                if (file_exists($file))
                    unlink($file);
            }

            if (Database::delete($deal_tbl, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'deal',
                    'title' => 'deal deleted',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'DealService::deleteADeal', ['deal' => ''], $th);
            Response::error(500, "An error occurred while deleting a deal");
        }
    }
}

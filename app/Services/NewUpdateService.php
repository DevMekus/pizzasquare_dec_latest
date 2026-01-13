<?php

namespace App\Services;

use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;

class NewUpdateService
{

    public static function fetchNewsUpdates($id)
    {
        $news_updates_tbl = Utility::$news_updates;

        try {
            return Database::joinTables(
                "$news_updates_tbl d",
                [],
                ["d.*"],
                [
                    "OR" => [
                        "d.id" => $id,
                        "d.news_id" => $id,

                    ]
                ],
                ["d.id" => $id]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'NewUpdateService::fetchNewsUpdats', ['news_update' => $id], $th);
            Response::error(500, "An error occurred while fetching a news update");
        }
    }


    public static function fetchAllNewsUpdates()
    {
        $news_updates_tbl = Utility::$news_updates;

        try {
            return Database::joinTables(
                "$news_updates_tbl d",
                [],
                ["d.*"],
                [],                
                [                   
                    "order" => "d.id DESC"
                ]

            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'NewUpdateService::fetchAllNewsUpdates', ['news_update' => ''], $th);
            Response::error(500, "An error occurred while fetching all news updates");
        }
    }


    public static function createNewUpdate($data)
    {
        try {
            $news_updates_tbl = Utility::$news_updates;

            $news_update = [
                'news_id' => Utility::generate_uniqueId(),               
                'title' => $data['title'],
                'description' => $data['description'],               
                'created_at' => date('y-m-d', time()),
            ];

            if (
                isset($_FILES['newsBanner']) &&
                $_FILES['newsBanner']['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['newsBanner']['tmp_name'])
            ) {
                $target_dir =   "public/UPLOADS/news_updates/";
                $news_banner = Utility::uploadDocuments('newsBanner', $target_dir);
                if (!$news_banner || !$news_banner['success']) Response::error(500, "Image upload failed");
                $news_update['image'] = $news_banner['files'][0];
            }

            if (Database::insert($news_updates_tbl, $news_update)) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'news_updates',
                    'title' => 'new news update created',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'NewUpdateService::createNewUpdate', ['news_update' => ''], $th);
            Response::error(500, "An error occurred while creating a news update");
        }
    }

    public static function updateANewsUpdate($id, $data, $news_update)
    {
        try {
            $news_updates_tbl = Utility::$news_updates;


            $update = [
              
                'title' => isset($data['title']) ? $data['title'] : $news_update['title'],
                'description' => isset($data['description']) ? $data['description'] : $news_update['description'],                
                'status' => isset($data['status']) ? $data['status'] : $news_update['status'],
            ];

            if (
                isset($_FILES['newsBanner']) &&
                $_FILES['newsBanner']['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['newsBanner']['tmp_name'])
            ) {
                $target_dir =   "public/UPLOADS/news_updates/";
                $news_banner = Utility::uploadDocuments('newsBanner', $target_dir);
                if (!$news_banner || !$news_banner['success']) Response::error(500, "Image upload failed");


                if (isset($news_update['image'])) {

                    $target_dir = "public/UPLOADS/news_updates/";

                    $filenameFromUrl = basename($news_update['image']);
                    $file = "../" . $target_dir  . $filenameFromUrl;
                    if (file_exists($file))
                        unlink($file);
                }

                $update['image'] = $news_banner['files'][0];
            }

            if (Database::update($news_updates_tbl, $update, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'news_updates',
                    'title' => 'news update updated',
                ]);
                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'NewsUpdateService::updateANewsUpdate', ['news_update' => $id], $th);
            Response::error(500, "An error occurred while updating news update");
        }
    }

    public static function deleteANewsUpdate($id,$news_update)
    {
        try {
            $news_updates = Utility::$news_updates;

            if (isset($news_update['image'])) {

                $target_dir = "public/UPLOADS/news_updates/";

                $filenameFromUrl = basename($news_update['image']);
                $file = "../" . $target_dir  . $filenameFromUrl;
                if (file_exists($file))
                    unlink($file);
            }

            if (Database::delete($news_updates, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'news_updates',
                    'title' => 'news update deleted',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'NewsUpdateService::deleteANewsUpdate', ['news_update' => ''], $th);
            Response::error(500, "An error occurred while deleting a news update");
        }
    }
}

<?php

namespace App\Controllers;


use App\Services\NewUpdateService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class NewsUpdateController
{

    public function listNewsUpdates()
    {
        try {
            $news_updates = NewUpdateService::fetchAllNewsUpdates();

            if (empty($news_updates))
                Response::error(404, "news update not found");

            Response::success($news_updates, "news updates found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'NewsUpdateController::listNewsUpdates', [], $e);
            Response::error(500, "Error fetching news updates");
        }
    }

    public function getNewsUpdateById($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $news_update = NewUpdateService::fetchNewsUpdates($id);
            if (empty($news_update))
                Response::error(404, "news update not found");

            Response::success($news_update, "news update found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'NewsUpdateController::getNewsUpdateById', [], $e);
            Response::error(500, "Error fetching news updates");
        }
    }

    public function postNewsUpdate()
    {
        try {
            $data = RequestValidator::validate([
                'title' => 'required|string',
            ], $_POST);

            $data = RequestValidator::sanitize($data);

            $news_update = NewUpdateService::fetchNewsUpdates($data['title']);
            if (!empty($news_update))
                Response::error(409, "news update already exist");

            if (NewUpdateService::createNewUpdate($data))
                Response::success([], "news update saved");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'NewsUpdateController::postNewsUpdate', [], $e);
            Response::error(500, "Error posting news update");
        }
    }

    public function updateNewsUpdate($id)
    {
        try {
            $id = RequestValidator::parseId($id);


            $data = RequestValidator::validate([], $_POST);
            $data = RequestValidator::sanitize($data);

            $news_update = NewUpdateService::fetchNewsUpdates($id);
            if (empty($news_update))
                Response::error(404, "news update not found");

            if (NewUpdateService::updateANewsUpdate($id, $data, $news_update[0]))
                Response::success([], "news update updated");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'NewsUpdateController::updateNewsUpdate', [], $e);
            Response::error(500, "Error updating news update");
        }
    }

    public function deleteNewsUpdate($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $news_update = NewUpdateService::fetchNewsUpdates($id);
            if (empty($news_update))
                Response::error(404, "news update not found");

            if (NewUpdateService::deleteANewsUpdate($id, $news_update[0]))
                Response::success([], "news update deleted");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'NewsUpdateController::deleteNewsUpdate', [], $e);
            Response::error(500, "Error deleting news update");
        }
    }
}

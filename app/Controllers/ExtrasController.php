<?php

namespace App\Controllers;

use App\Services\ExtrasService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class ExtrasController
{
    public function listExtras()
    {
        try {
            $extra = ExtrasService::fetchAllExtras();

            if (empty($extra))
                Response::error(404, "extras not found");

            Response::success($extra, "extras found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ExtrasController::listExtras', [], $e);
            Response::error(500, "Error fetching extras");
        }
    }

    public function getExtraById($id)
    {
        try {

            $id = RequestValidator::parseId($id);
            $extra = ExtrasService::fetchExtras($id);

            if (empty($extra))
                Response::error(404, "extras not found");

            Response::success($extra, "extras found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ExtrasController::getExtrasById', [], $e);
            Response::error(500, "Error fetching extras");
        }
    }

    public function postExtras()
    {
        try {
            $data = RequestValidator::validate([
                'category_id'     => 'require',
                'extras' => 'required|address',
                'extras_price' => 'required|int',
            ]);
            $data = RequestValidator::sanitize($data);

            $extra = ExtrasService::fetchExtras($data['extras']);

            if (!empty($extra))
                Response::error(409, "extra already exist");

            if (ExtrasService::createExtras($data))
                Response::success([], "Extras saved");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ExtrasController::postExtra', [], $e);
            Response::error(500, "Error posting extra");
        }
    }


    public function updateExtras($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $data = RequestValidator::validate([
                'category_id'     => 'require',
                'extras' => 'required|address',
                'extras_price' => 'required|int',
            ]);
            $data = RequestValidator::sanitize($data);

            $extra = ExtrasService::fetchExtras($id);

            if (empty($extra))
                Response::error(404, "extra not found");

            if (ExtrasService::updateExtras($id, $data))
                Response::success([], "Extras updated");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ExtrasController::updateExtras', [], $e);
            Response::error(500, "Error updating extra");
        }
    }


    public function deleteExtras($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $extra = ExtrasService::fetchExtras($id);

            if (empty($extra))
                Response::error(404, "extra not found");


            if (ExtrasService::deleteExtras($id))
                Response::success([], "Extras deleted");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ExtrasController::deleteExtras', [], $e);
            Response::error(500, "Error deleting extra");
        }
    }
}

<?php

namespace App\Controllers;


use App\Services\DealService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class DealController
{

    public function listDeals()
    {
        try {
            $deals = DealService::fetchAllDeals();

            if (empty($deals))
                Response::error(404, "deals not found");

            Response::success($deals, "deals found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'DealController::listDeals', [], $e);
            Response::error(500, "Error fetching deals");
        }
    }

    public function getDealById($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $deal = DealService::fetchADeal($id);
            if (empty($deal))
                Response::error(404, "deal not found");

            Response::success($deal, "deal found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'DealController::getDealById', [], $e);
            Response::error(500, "Error fetching deals");
        }
    }

    public function postDeal()
    {
        try {
            $data = RequestValidator::validate([
                'title' => 'required|string',
            ], $_POST);

            $data = RequestValidator::sanitize($data);

            $deal = DealService::fetchADeal($data['title']);
            if (!empty($deal))
                Response::error(409, "deal already exist");


            if (DealService::createNewDeal($data))
                Response::success([], "deal saved");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'DealController::postDeal', [], $e);
            Response::error(500, "Error posting deal");
        }
    }

    public function updateDeal($id)
    {
        try {
            $id = RequestValidator::parseId($id);


            $data = RequestValidator::validate([], $_POST);
            $data = RequestValidator::sanitize($data);

            $deal = DealService::fetchADeal($id);
            if (empty($deal))
                Response::error(404, "deal not found");

            if (DealService::updateADeal($id, $data, $deal[0]))
                Response::success([], "deal updated");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'DealController::updateDeal', [], $e);
            Response::error(500, "Error updating deal");
        }
    }

    public function deleteDeal($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $deal = DealService::fetchADeal($id);
            if (empty($deal))
                Response::error(404, "deal not found");

            if (DealService::deleteADeal($id))
                Response::success([], "deal deleted");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'DealController::updateDeal', [], $e);
            Response::error(500, "Error updating deal");
        }
    }
}

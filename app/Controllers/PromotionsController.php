<?php

namespace App\Controllers;


use App\Services\PromotionsService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class PromotionsController
{

    public function listPromotions()
    {
        try {
            $promotions = PromotionsService::fetchAllPromotions();

            if (empty($promotions))
                Response::error(404, "promotion not found");

            Response::success($promotions, "promotions found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'PromotionsController::listPromotions', [], $e);
            Response::error(500, "Error fetching promotions");
        }
    }

    public function getPromotionById($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $promotion = PromotionsService::fetchPromotion($id);
            if (empty($promotion))
                Response::error(404, "promotion not found");

            Response::success($promotion, "promotion found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'PromotionsController::getPromotionById', [], $e);
            Response::error(500, "Error fetching promotions");
        }
    }

    public function postPromotion()
    {
        try {
            $data = RequestValidator::validate([
                'title' => 'required|string',
            ], $_POST);

            $data = RequestValidator::sanitize($data);

            $promotion = PromotionsService::fetchPromotion($data['title']);
            if (!empty($promotion))
                Response::error(409, "promotion already exist");

            if (PromotionsService::createPromotion($data))
                Response::success([], "promotion saved");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'PromotionsController::postPromotion', [], $e);
            Response::error(500, "Error saving promotion");
        }
    }

    public function updatePromotion($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $previous = PromotionsService::fetchPromotion($id);
            if (empty($previous))
                Response::error(404, "promotion not found");

            $data = RequestValidator::validate([], $_POST);

            $data = RequestValidator::sanitize($data);

            if (PromotionsService::updatePromotion($id, $data, $previous))
                Response::success([], "promotion updated");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'PromotionsController::updatePromotion', [], $e);
            Response::error(500, "Error updating promotion");
        }
    }

    public function deletePromotion($id)
    {
        try {
            $id = RequestValidator::parseId($id);

            $promotion = PromotionsService::fetchPromotion($id);
            if (empty($promotion))
                Response::error(404, "promotion not found");

            if (PromotionsService::deletePromotion($id, $promotion[0]))
                Response::success([], "promotion deleted");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'PromotionsController::deletePromotion', [], $e);
            Response::error(500, "Error deleting promotion");
        }
    }

}
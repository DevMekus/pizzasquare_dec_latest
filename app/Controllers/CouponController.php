<?php

namespace App\Controllers;

use App\Services\CouponService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class  CouponController
{


    public function listCoupons()
    {
        try {
            $coupon = CouponService::fetchAllCoupons();

            if (empty($coupon))
                Response::error(404, "coupons not found");

            Response::success($coupon, "coupons found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CouponController::listCoupons', [], $e);
            Response::error(500, "Error fetching coupons");
        }
    }

    public function getCouponById($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $coupon = CouponService::fetchACoupon($id);

            if (empty($coupon))
                Response::error(404, "coupon not found");

            Response::success($coupon, "coupon found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CouponController::getCouponById', [], $e);
            Response::error(500, "Error fetching coupon");
        }
    }

    public function postCoupon()
    {
        try {
            $data = RequestValidator::validate([
                'coupon' => 'required|string',
                'discount' => 'required|int',
            ]);

            $coupon = CouponService::fetchACoupon($data['coupon']);
            if (!empty($coupon))
                Response::error(409, "coupon already exist");
            $data = RequestValidator::sanitize($data);

            if (CouponService::createNewCoupon($data))
                Response::success([], "coupon saved");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CouponController::postCoupon', [], $e);
            Response::error(500, "Error fetching coupon");
        }
    }

    public function updateCoupon($id)
    {
        try {
            $data = RequestValidator::validate([
                'coupon' => 'required|string',
                'discount' => 'required|int',
            ]);
            $id = RequestValidator::parseId($id);

            $coupon = CouponService::fetchACoupon($id);
            if (empty($coupon))
                Response::error(404, "coupon not found");
            $data = RequestValidator::sanitize($data);

            if (CouponService::updateCoupon($id, $data))
                Response::success([], "coupon updated");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CouponController::updateCoupon', [], $e);
            Response::error(500, "Error updating coupon");
        }
    }

    public function deleteCoupon($id)
    {
        try {
            $id = RequestValidator::parseId($id);

            $coupon = CouponService::fetchACoupon($id);
            if (empty($coupon))
                Response::error(404, "coupon not found");

            if (CouponService::deleteCoupon($id))
                Response::success([], "coupon deleted");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'CouponController::deleteCoupon', [], $e);
            Response::error(500, "Error deleting coupon");
        }
    }
}

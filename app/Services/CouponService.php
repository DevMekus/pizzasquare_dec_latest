<?php

namespace App\Services;

use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;

class CouponService
{

    public static function fetchACoupon($id)
    {
        $coupon_tbl = Utility::$coupons;
        try {
            return Database::joinTables(
                "$coupon_tbl c",
                [],
                ["c.*"],
                [
                    "OR" => [
                        "c.id" => $id,
                        "c.coupon" => $id,
                    ]
                ],
                ["c.id" => $id]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CouponService::fetchACoupon', ['coupon' => $id], $th);
            Response::error(500, "An error occurred while fetching a coupon");
        }
    }

    public static function fetchAllCoupons()
    {
        $coupon_tbl = Utility::$coupons;

        try {
            return Database::joinTables(
                "$coupon_tbl c",
                [],
                ["c.*"],

            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CouponService::fetchAllCoupons', ['coupon' => ''], $th);
            Response::error(500, "An error occurred while fetching all coupon");
        }
    }

    public static function createNewCoupon($data)
    {
        try {
            $coupon_tbl = Utility::$coupons;
            $existing = Database::find($coupon_tbl, $data['coupon'], 'coupon');

            if ($existing) {
                Response::error(409, "coupon already exists");
            }

            //convert discount to percent. Expecting whole number like 10
            $discountInWhole = intval($data['discount']);
            $discountInPercent = $discountInWhole / 100;

            $coupon = [
                'coupon' => $data['coupon'],
                'discount' =>  floatval(($discountInPercent)),
            ];

            if (Database::insert($coupon_tbl, $coupon)) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'coupon',
                    'title' => 'coupon added',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CouponService::createNewCoupon', ['category' => ''], $th);
            Response::error(500, "An error occurred while creating coupon");
        }
    }

    public static function updateCoupon($id, $data)
    {
        try {
            $coupon_tbl = Utility::$coupons;
            $existing = self::fetchACoupon($id);

            if (empty($existing)) {
                Response::error(404, "coupon not found");
            }

            $coupon = $existing[0];
            $discountInPercent = 0;

            if (isset($data['discount'])) {
                $discountInWhole = intval($data['discount']);
                $discountInPercent = $discountInWhole / 100;
            } else {
                $discountInPercent = $coupon['discount'];
            }

            $discountInWhole = intval($data['discount']);
            $discountInPercent = $discountInWhole / 100;

            $update = [
                'coupon' => isset($data['city']) ? $data['city'] : $coupon['city'],
                'discount' => floatval($discountInPercent),
            ];

            if (Database::update($coupon_tbl, $update, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'coupon',
                    'title' => 'coupon updated',
                ]);
                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CouponService::updateCoupon', ['coupon' => $id], $th);
            Response::error(500, "An error occurred while updating coupon");
        }
    }

    public static function deleteCoupon($id)
    {
        try {
            $coupon_tbl = Utility::$coupons;

            $existing = self::fetchACoupon($id);

            if (empty($existing)) {
                Response::error(404, "coupon not found");
            }


            if (Database::delete($coupon_tbl, ['id' => $id])) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'coupon',
                    'title' => 'coupon deleted',
                ]);

                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CouponService::deleteCoupon', ['coupon' => ''], $th);
            Response::error(500, "An error occurred while deleting coupon");
        }
    }
}

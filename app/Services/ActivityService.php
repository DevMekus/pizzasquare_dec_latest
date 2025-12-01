<?php

namespace App\Services;

use App\Utils\Response;
use App\Utils\Utility;
use configs\Database;

class ActivityService
{


    public static function fetchActivity($id)
    {
        $log_tbl = Utility::$loginactivity;
        $profile_tbl = Utility::$profile_tbl;

        try {
            return Database::joinTables(
                "$log_tbl l",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$profile_tbl p",
                        "on" => "l.userid = p.userid"
                    ]
                ],
                ["l.*", "p.fullname"],
                ["l.$id" => $id],
                ["order" => "l.id DESC"]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ActivityService::fetchActivity', ["$id" => $id], $th);
            Response::error(500, "An error occurred while fetching activity logs");
        }
    }


    public static function fetchAllActivity()
    {
        $log_tbl = Utility::$loginactivity;
        $profile_tbl = Utility::$profile_tbl;

        try {
            return Database::joinTables(
                "$log_tbl l",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$profile_tbl p",
                        "on" => "l.userid = p.userid"
                    ]
                ],
                ["l.*", "p.fullname"],
                [],
                ["order" => "l.id DESC"]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ActivityService::fetchActivity', ["userid" => $_SESSION['userid']], $th);
            Response::error(500, "An error occurred while fetching activity logs");
        }
    }

    public static function saveActivity($data)
    {
        $log_tbl = Utility::$loginactivity;
        $device = Utility::getUserDevice();
        $ip     = Utility::getUserIP();
        try {
            $activity = [
                'logid' => Utility::generate_uniqueId(10),
                'userid' => $data['userid'] ?? '',
                'type' => $data['type'],
                'title' => $data['title'],
                'status' => $data['status'] ?? 'success',
                'ip' => $data['ip'] ?? $ip,
                'device' => $device,
            ];

            return Database::insert($log_tbl, $activity);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ActivityService::saveActivity', ["userid" => $_SESSION['userid']], $th);
            Response::error(500, "An error occurred while saving activity logs");
        }
    }

    public static function deleteActivity($id)
    {
        try {
            $log_tbl = Utility::$loginactivity;
            return Database::delete($log_tbl, [$id => $id]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ActivityService::deleteActivity', ["userid" => $_SESSION['userid']], $th);
            Response::error(500, "An error occurred while deleting activity logs");
        }
    }

    public static function clearActivity()
    {
        try {
            $log_tbl = Utility::$loginactivity;
            return Database::delete($log_tbl, []);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ActivityService::clearActivity', ["userid" => $_SESSION['userid']], $th);
            Response::error(500, "An error occurred while deleting all activity logs");
        }
    }
}

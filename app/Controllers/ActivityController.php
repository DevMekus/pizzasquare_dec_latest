<?php

namespace App\Controllers;

use App\Services\ActivityService;
use App\Utils\Response;
use App\Utils\RequestValidator;
use App\Utils\Utility;

class ActivityController
{

    public function listActivities()
    {
        try {
            $activities = ActivityService::fetchAllActivity();

            if (empty($activities)) {
                Response::error(404, "activities not found");
                return;
            }

            Response::success($activities, "activities found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ActivityController::listActivities', [], $e);
            Response::error(500, "Error fetching activities");
        }
    }

    public function getActivityById($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $activities = ActivityService::fetchActivity($id);

            if (empty($activities)) {
                Response::error(404, "activities not found");
                return;
            }
            Response::success($activities, "activities found");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ActivityController::getActivityById', [], $e);
            Response::error(500, "Error fetching activities");
        }
    }

    public function postActivity()
    {
        try {
            $data = RequestValidator::validate([
                'userid' => 'required|string',
                'type' => 'required|string',
                'title' => 'required|string',
                'status' => 'required|string',
                'ip' => 'required|string',
            ]);

            $data = RequestValidator::sanitize($data);

            if (ActivityService::saveActivity($data))
                Response::success([], "activity saved");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ActivityController::createActivity', [], $e);
            Response::error(500, "Error creating activities");
        }
    }

    public function deleteActivity($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $activities = ActivityService::fetchActivity($id);

            if (empty($activities)) {
                Response::error(404, "activities not found");
                return;
            }
            if (ActivityService::deleteActivity($id))
                Response::success([], "activity deleted");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'ActivityController::deleteActivity', [], $e);
            Response::error(500, "Error creating activities");
        }
    }
}

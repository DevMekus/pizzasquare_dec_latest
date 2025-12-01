<?php

namespace App\Middleware;

use App\Utils\Response;
use App\Middleware\AuthMiddleware;

class AdminOnlyMiddleware
{
    public function handle()
    {
        $userData = AuthMiddleware::verifyToken();

        if (!isset($userData['role']) || 
         ((string)$userData['role'] !== '4' && (string)$userData['role'] !== '2')) {
            Response::error(403, 'Access denied');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['userid'] = $userData['userid'];

        return true;
    }
}

<?php

use App\Utils\Utility;

Utility::verifySession();

$role   = $_SESSION['role'];
$userid = $_SESSION['userid'];
$user = null;

// Refresh user profile if not cached or expired
$cacheDuration = 18000;
$shouldRefresh = !isset($_SESSION['user_profile'])
    || !isset($_SESSION['profile_cached_at'])
    || (time() - $_SESSION['profile_cached_at']) > $cacheDuration;

if ($shouldRefresh) {
    $url        = BASE_URL . "api/v1/users/$userid";
    $getProfile = Utility::requestClient($url);
    $userArray = $getProfile['data'] ?? null;

    if (empty($userArray)) {
        session_unset();
        session_destroy();
        header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
        exit;
    }

    $user = is_array($userArray) && isset($userArray[0]) ? $userArray[0] : $userArray;

    $_SESSION['user_profile']   = $user;
    $_SESSION['profile_cached_at'] = time();
} else {
    $user = $_SESSION['user_profile'];
}

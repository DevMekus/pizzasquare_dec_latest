<?php

use App\Utils\Utility;

Utility::verifySession();

$role   = $_SESSION['role'];
$userid = $_SESSION['userid'];
$user = null;

// Refresh user profile if not cached or expired
$cacheDuration = 86400;
$shouldRefresh = !isset($_SESSION['user_profile'])
    || !isset($_SESSION['profile_cached_at'])
    || (time() - $_SESSION['profile_cached_at']) > $cacheDuration;

if ($shouldRefresh) {
    session_destroy();
    header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
    exit;
} else {
    $user = $_SESSION['user_profile'];
}

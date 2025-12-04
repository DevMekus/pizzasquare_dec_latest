<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define("BRAND_NAME", "Pizza Square Nigeria");
define("BRAND_PHONE", "080 555 44014");
define("BRAND_EMAIL", "info@pizzasquare.ng");
define("ADMIN_EMAIL", "info@pizzasquare.ng");
define("COMPANY_ADDRESS", "99 Chime Avenue, New Haven, Enugu.");
define("AUTH_INTRO", "Pizza Square Nigeria offers a wide range of Pizza and Shawarma menu crafted with authentic Italian recipes while maintaining original quality and classic taste.");
define("TAG", "Made with Love From Italy");


// ===========================
// ROOT AND URL DEFINITIONS
// ===========================

// Physical rooth path (for require/ include)
define('ROOT_PATH', dirname(__FILE__));

// Public folder
define('PUBLIC_PATH', ROOT_PATH . '/public');

//Site base URL
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

define('BASE_URL', ($https ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/pizzasq/');
define('BASE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/pizzasq/');



//API
define("API_URL", BASE_URL . "api/");

// ===========================
// AUTOLOAD CORE FILES
// ===========================

if (file_exists(ROOT_PATH . '/app/Utils/Utility.php')) {
    require_once ROOT_PATH . '/app/Utils/Utility.php';
}

if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// ===========================
// ENVIRONMENT DETECTION
// ===========================

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    define("ENVIRONMENT", 'development');
} else {
    define('ENVIRONMENT', 'production');
}

if (ENVIRONMENT == 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

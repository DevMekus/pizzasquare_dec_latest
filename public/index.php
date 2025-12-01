<?php
require_once dirname(__DIR__) . '/siteConfig.php';

$url = isset($_GET['url']) ? trim($_GET['url'], '/') : 'home';
$urlParts = explode('/', $url);

$page = $urlParts[0];
$id = $urlParts[1] ?? null;

$metaTitle = BRAND_NAME;
$metaDescription = "AI powered multivendor website that offers full customization ";
$metaKeywords = "Ai, e-commerce, brand, shop";

function getPagePath($page, $id = null)
{
    global $urlParts;

    // Check for flat file first
    $basePath = ROOT_PATH . '/pages/' . $page . '.php';
    if (file_exists($basePath)) {
        return $basePath;
    }

    // Dynamically build and check nested paths
    $currentPath = ROOT_PATH . '/pages';
    for ($i = 0; $i < count($urlParts); $i++) {
        $currentPath .= '/' . $urlParts[$i];
        $possibleFile = $currentPath . '.php';

        if (file_exists($possibleFile)) {
            return $possibleFile;
        }
    }

    return false;
}


$pagePath = getPagePath($page, $id);

if ($pagePath) {
    if ($id) {
        $_GET['id'] = $id;
    }


    switch ($page) {

        case 'home':
            $metaTitle = 'Welcome to ' . BRAND_NAME;
            $metaDescription = 'Order your favorite pizzas at PizzaSquare. Delicious, fresh, and delivered hot!';
            $metaKeywords = 'pizza, order pizza, delivery, PizzaSquare';
            break;

        case 'hot-deals':
            $metaTitle = 'Best Pizza Deals | ' . BRAND_NAME;
            $metaDescription = 'Discover amazing pizza deals and discounts at PizzaSquare. Save big while enjoying your favorite flavors!';
            $metaKeywords = 'pizza deals, discounts, offers, PizzaSquare deals';
            break;

        case 'track-order':
            $metaTitle = 'Track Your Order | ' . BRAND_NAME;
            $metaDescription = 'Easily track your pizza order in real-time at PizzaSquare. Stay updated from oven to your doorstep.';
            $metaKeywords = 'track order, pizza delivery, order status, PizzaSquare tracking';
            break;

        case 'checkout':
            $metaTitle = 'Secure Checkout | ' . BRAND_NAME;
            $metaDescription = 'Complete your pizza order securely with PizzaSquare. Fast checkout and multiple payment options available.';
            $metaKeywords = 'checkout, pizza order, secure payment, PizzaSquare checkout';
            break;
        case 'auth':
            $authPage = $_GET['id'] ?? '';
            if (strpos($authPage, 'login') !== false) {
                $metaTitle = 'Login to Your Account | ' . BRAND_NAME;
                $metaDescription = 'Access your personalized dashboard to manage your orders, products, and settings. Secure login for customers, vendors, and admins.';
                $metaKeywords = 'login, sign in, dealer access, customer login, admin login, multiuser platform';
            } else {
                $metaTitle = 'Authentication | ' . BRAND_NAME;
                $metaDescription = 'Secure authentication pages for accessing your ' . BRAND_NAME . ' account.';
                $metaKeywords = 'auth, login, register, forgot password';
            }
            break;
        case 'secure':
            $dashPage = $_GET['id'] ?? '';
            $metaTitle = ucfirst($dashPage) . ' | ' . BRAND_NAME;
            $metaDescription = 'Your personalized dashboard';
            $metaKeywords = 'Secure, dashboard, ecommerce, cars';
            break;


        default:
            $metaTitle = BRAND_NAME;
            $metaDescription = 'Enjoy the best pizza in town with PizzaSquare!';
            $metaKeywords = 'pizza, PizzaSquare, order online';
            break;
    }

    require_once $pagePath;
} else {
    require_once ROOT_PATH . '/pages/404.php';
}

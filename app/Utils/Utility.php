<?php

namespace App\Utils;


class Utility
{
    public static  $API_ROUTE = "/pizzasq/api";
    public static $siteName = '';

    public static $accounts = 'accounts_tbl';
    public static $profile_tbl = 'users';
    public static $categories = 'categories';
    public static $category_size_stock = 'category_size_stock';
    public static $products = 'products';
    public static $product_sizes = 'product_sizes';
    public static $product_stock = 'product_stock';
    public static $sizes = 'sizes';
    public static $stock_movements = 'stock_movements';
    public static $sessions_tbl = 'sessions';
    public static $loginactivity = 'loginactivity';
    public static $roles = 'roles';
    public static $extras = 'extras';
    public static $deals = 'deals';
    public static $city = 'city';
    public static $coupons = 'coupons';
    public static $vat_tbl = 'vat';
    public static $payments = 'payments';
    public static $order_items = 'order_items';
    public static $orders = 'orders';
    public static $order_toppings = 'order_toppings';
     

    public static function debugger()
    {
        $logFile = __DIR__ . "/debug.log";
        $message = "[" . date("Y-m-d H:i:s") . "] Code reached here\n";
        file_put_contents($logFile, $message, FILE_APPEND);
    }

    public static function makeDirectory(string $userDir)
    {
        /**
         * Create a new directory
         */

        if (!file_exists($userDir)) {
            if (mkdir($userDir, 0777, true)) {
                chmod($userDir, 0777);
                return true;
            }
            return false;
        }
    }

    public static function generate_uniqueId()
    {
        try {

            $number = random_int(1000000, 9999999);
            return "ps-" . $number;
        } catch (\Exception $e) {
            return false;
        }
    }


   

    static function log(
        string $message,
        string $level = 'info',
        string $context = 'general',
        array $extra = [],
        ?\Throwable $exception = null
    ) {
        $logDir = __DIR__ . "/../../logs";

        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . '/' . date('Y-m-d') . '.log';
        $logEntry = [
            'timestamp' => date('c'),
            'level' => strtolower($level),
            'context' => $context,
            'message' => $message,
            'extra' => $extra
        ];

        if ($exception) {
            $logEntry['exception'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        file_put_contents($logFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND);
    }


    public static function uploadDocuments(string $inputName, string $targetDir)
    {
        try {
            if (!isset($_FILES[$inputName]) || empty($_FILES[$inputName]['name'][0])) {
                Utility::log("No files uploaded via input: $inputName", 'error');
                Response::error(500, "No files uploaded.");
            }

            $uploadedPaths = [];
            $errors = [];


            // Normalize single vs multiple files
            if (!is_array($_FILES[$inputName]['name'])) {
                // Single file → convert to array format
                $_FILES[$inputName] = [
                    'name'     => [$_FILES[$inputName]['name']],
                    'type'     => [$_FILES[$inputName]['type']],
                    'tmp_name' => [$_FILES[$inputName]['tmp_name']],
                    'error'    => [$_FILES[$inputName]['error']],
                    'size'     => [$_FILES[$inputName]['size']]
                ];
            }

            $files = [];
            foreach ($_FILES[$inputName]['name'] as $i => $name) {
                $files[] = [
                    'name' => $_FILES[$inputName]['name'][$i],
                    'type' => $_FILES[$inputName]['type'][$i],
                    'tmp_name' => $_FILES[$inputName]['tmp_name'][$i],
                    'error' => $_FILES[$inputName]['error'][$i],
                    'size' => $_FILES[$inputName]['size'][$i]
                ];
            }

            // Prepare absolute target path
            $absoluteTargetDir = rtrim(BASE_DIR, '/') . '/' . trim($targetDir, '/') . '/';
            if (!is_dir($absoluteTargetDir)) {
                mkdir($absoluteTargetDir, 0755, true);
            }

            foreach ($files as $file) {
                $name = $file['name'];
                $tmpName = $file['tmp_name'];
                $error = $file['error'];
                $size = $file['size'];

                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowedExtensions = [
                    'pdf',  // Safe to preview & store
                    'doc',
                    'docx',
                    'xls',
                    'xlsx',
                    'jpeg',
                    'jpg',
                    'png',
                    'webp', // Modern + smaller size than JPG/PNG
                ];
                $allowedMimeTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if ($error !== UPLOAD_ERR_OK) {
                    Utility::log("File upload error ($error) for $name", 'error');
                    Response::error(500, "File upload error ($error) for $name");
                }

                if (!in_array($extension, $allowedExtensions)) {
                    Utility::log("Invalid file extension ($extension) for file: $name", 'error');
                    $errors[] = ['file' => $name, 'reason' => 'Invalid file extension'];
                    Response::error(500, "Invalid file extension");
                }

              
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $tmpName);
                    finfo_close($finfo);

                    if (!in_array($mime, $allowedMimeTypes)) {
                        $errors[] = ['file' => $name, 'reason' => 'Invalid MIME type'];
                        continue;
                    }
                } else {
                    Utility::log("WARNING: fileinfo extension missing. Skipping MIME validation.", 'warning');
                }


                if ($size > 10 * 1024 * 1024) {
                    Utility::log("File too large ($size bytes) for file: $name", 'error');
                    $errors[] = ['file' => $name, 'reason' => 'File size exceeds 10MB'];
                    Response::error(500, "File size exceeds 10MB");
                }

                $uniqueName = uniqid('', true) . '.' . $extension;
                $destinationPath = $absoluteTargetDir . $uniqueName;

                if (!move_uploaded_file($tmpName, $destinationPath)) {
                    Utility::log("Failed to move file: $name to $destinationPath", 'error');
                    $errors[] = ['file' => $name, 'reason' => 'Failed to move file'];
                    Response::error(500, "Failed to move file: $name to $destinationPath");
                }

                // Construct full public URL
                $publicUrl = rtrim(BASE_URL, '/') . '/' . trim($targetDir, '/') . '/' . $uniqueName;
                $uploadedPaths[] = $publicUrl;
            }

            return [
                'success' => count($uploadedPaths) > 0,
                'files' => $uploadedPaths,
                'errors' => $errors
            ];
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'Utility::uploadDocuments', [], $th);
            Response::error(500, "An error occurred while uploading file");
        }
    }




    public static function verifySession()
    {

        if (!isset($_SESSION['role'], $_SESSION['token'])) {
            header("Location: " . BASE_URL . "auth/login?f-bk=UNAUTHORIZED");
            exit;
        }
        if (self::isJwtExpired($_SESSION['token']))
            header("Location: " . BASE_URL . "auth/login?f-bk=UNAUTHORIZED");
    }

    static function isJwtExpired($token)
    {
        $parts = explode(".", $token);
        if (count($parts) !== 3) return true;
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!isset($payload['exp'])) return true;
        return $payload['exp'] < time();
    }

    public  static function requestClient($url)
    {


        $token = $_SESSION['token'] ?? null;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //set headers
        $headers = [];
        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpCode === 200) ? json_decode($response, true) : false;
    }


    /**
     * Get the current route relative to the base folder.
     *
     * @return string
     */
    public static function currentRoute(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $baseFolder = '/pizzasquare_v2';



        if (strpos($requestUri, $baseFolder) === 0) {
            $requestUri = substr($requestUri, strlen($baseFolder));
        }



        return trim(parse_url($requestUri, PHP_URL_PATH) ?? '', '/');
    }
    /**
     * Get the current route relative to the base folder.
     *
     * @return string
     */


    public static function truncateText(string $text, int $limit = 100): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return mb_substr($text, 0, $limit) . '...';
    }

    /**
     * Get the client's IP address.
     *
     * @return string
     */
    public static function getUserIP(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get the client's device user agent string.
     *
     * @return string
     */
    public static function getUserDevice(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

   
}

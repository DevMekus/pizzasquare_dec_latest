<?php

namespace App\Services;

use App\Utils\Response;
use Throwable;

/**
 * Class Paystack
 *
 * Handles interactions with Paystack's API, including payment verification.
 *
 * @package App\Services
 */
class Paystack
{
    /**
     * Verify a Paystack transaction using its reference.
     *
     * @param string $reference The transaction reference to verify.
     * @return array {
     *     @type bool   $status   Whether the verification was successful.
     *     @type string $message  Response message from Paystack or error reason.
     *     @type array  $data     (optional) Additional Paystack data (amount, customer, etc.).
     * }
     */
    public static function verifyPaystackPayment(string $reference): array
    {
        $secretKey = $_ENV['PAYSTACK_SECRET'];
        $url       = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

        try {
            $ch = curl_init();
            if ($ch === false) {
                return [
                    "status"  => false,
                    "message" => "Failed to initialize cURL session"
                ];
            }

            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false, // ⚠️ Always true in production
                CURLOPT_HTTPHEADER     => [
                    "Authorization: Bearer {$secretKey}",
                    "Cache-Control: no-cache",
                ],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $errorMsg = curl_error($ch);
                curl_close($ch);
                return [
                    "status"  => false,
                    "message" => "cURL error: {$errorMsg}"
                ];
            }

            curl_close($ch);

            if ($httpCode !== 200) {
                return [
                    "status"   => false,
                    "message"  => "HTTP error code: {$httpCode}",
                    "response" => $response
                ];
            }

            $result = json_decode($response, true);

            if (!is_array($result) || !isset($result['status'])) {
                return [
                    "status"  => false,
                    "message" => "Invalid response format from Paystack"
                ];
            }

            if (!$result['status']) {
                return [
                    "status"  => false,
                    "message" => $result['message'] ?? "Verification failed"
                ];
            }

            if (isset($result['data']['status']) && $result['data']['status'] === "success") {
                return [
                    "status" => true,
                    "data"   => $result['data']
                ];
            }

            return [
                "status"  => false,
                "message" => "Payment not successful",
                "data"    => $result['data'] ?? []
            ];
        } catch (Throwable $e) {
            return [
                "status"  => false,
                "message" => "Exception occurred: " . $e->getMessage()
            ];
        }
    }
}

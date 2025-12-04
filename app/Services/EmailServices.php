<?php
namespace App\Services;

use App\Utils\MailClient;

class EmailServices{

    public static function registrationEmail($data)
    {

        $templateData = [
            '{{logo_url}}' => BASE_URL . 'assets/images/logo_white.png',
            '{{banner_image_url}}' => BASE_URL . 'assets/images/emails/registration_banner.jpeg',
            '{{user_name}}' => $data['fullname'],
            '{{user_email}}' => $data['email_address'],
            '{{user_password}}' => $data['user_password'],
            '{{site_name}}' => BRAND_NAME,
            '{{company_address}}' => COMPANY_ADDRESS,
            '{{login_link}}' => BASE_URL . 'auth/login',
            '{{support_url}}' => BASE_URL . 'contact-us',
            '{{current_year}}' => date('Y'),
        ];

        return MailClient::sendMail(
            $data['email_address'],
            'Welcome to ' . BRAND_NAME,
            ROOT_PATH . '/app/Services/templates/registration.html',
            $templateData,
            $data['fullname']
        );
    }

    public static function passwordResetEmail($data)
    {
        $resetLink = BASE_URL . "auth/reset-password?token=$data[reset_token]&email=" . urlencode($data['email_address']);

        $templateData = [
            '{{logo_url}}' => BASE_URL . 'assets/images/logo_white.png',
            '{{user_name}}' => $data['fullname'],
            '{{platform_name}}' => BRAND_NAME,
            '{{reset_link}}' => $resetLink,
            '{{support_url}}' => BASE_URL . 'contact-us',
            '{{current_year}}' => date('Y'),
            '{{user_email}}' => $data['email_address']
        ];

        return MailClient::sendMail(
                $data['email_address'],
                'Account Recovery',
                ROOT_PATH . '/app/Services/templates/reset_password.html',
                $templateData,
                $data['fullname']
        );
    }

    public static function sendInsufficientStockNotification($product_id, $size_id, $required_qty)
    {   
           
    }

    public static function sendOrderConfirmationEmail($data){
        

    }
    
}
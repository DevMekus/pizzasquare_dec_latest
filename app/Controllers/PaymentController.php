<?php
namespace App\Controllers;
use App\Services\Paystack;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class PaymentController{

    public function initiatePayment($order_id, $amount, $method){
        // Logic to initiate payment
        //create payment record
    }

    public function confirmPayment(){
       try {
            $data = RequestValidator::validate([
                    'reference' => 'required|address',
            ]);
            
            $verify = Paystack::verifyPaystackPayment($data['reference']);
            if (!$verify['status']) {
                    Response::error(401, "Verification failed. " . $verify['message']);
            }
        
            Response::success([], 'Payment verified successfully');
       } catch (\Throwable $th) {
        //throw $th;
       }

          
    }

    public function splitPayment($order_id, $payments){
        // Logic to split payment among multiple methods
        //create multiple payment records
    }

    public function getPayments($order_id){
        // Logic to retrieve all payments for an order
        //fetch payment records
    }
}
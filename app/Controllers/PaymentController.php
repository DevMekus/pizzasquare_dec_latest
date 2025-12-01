<?php
namespace App\Controllers;

class PaymentController{

    public function initiatePayment($order_id, $amount, $method){
        // Logic to initiate payment
        //create payment record
    }

    public function confirmPayment($payment_id, $status, $reference){
        // Logic to confirm payment webhook/callback
        //update payment record
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
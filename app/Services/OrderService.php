<?php
namespace App\Services;
use App\Utils\Utility;
use App\Utils\Response;
use configs\Database;
use App\Services\ActivityService;
use App\Utils\MailClient;

class OrderService{

    public static function fetchVAT(){
         $vat_tbl = Utility::$vat_tbl;

        try {
            return Database::joinTables(
                "$vat_tbl v",
                [],
                [
                    "v.*"
                ],
                [],

            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'OrderService::fetchVAT', ['Order' => ''], $th);
            Response::error(500, "An error occurred while fetching VAT");
        }
    }
    
}
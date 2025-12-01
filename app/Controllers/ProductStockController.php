<?php

namespace App\Controllers;

use App\Services\ProductStockService;
use App\Utils\Response;
use App\Utils\RequestValidator;

class ProductStockController
{
    /**
     * GET /product-stock/{product_id}
     */
    public function productStocks($product_id)
    {
        $stocks = ProductStockService::getProductStock($product_id);
        if ($stocks === false) {
            Response::error("Failed to fetch product stocks.");
        }

        if (empty($stocks)) {
            Response::error("No stocks found for the specified product.", [], 404);
        }
        
        Response::success("Product stocks fetched successfully.", $stocks);
    }

    public function allProductStocks()
    {
        $stocks = ProductStockService::getProductStocks();
       
        if ($stocks === false) {
            Response::error("Failed to fetch product stocks.");
        }

        if (empty($stocks)) {
            Response::error("No product stocks found.", [], 404);
        }
        
        Response::success($stocks, "Product stocks fetched successfully.");
    }

    /**
     * GET /category-stock/{category_id}
     */
    public function categoryStocks($category_id){
        
        $category_id = RequestValidator::parseId($category_id);
        $category_id = intval($category_id);
        $stock = ProductStockService::getCategoryStock($category_id);
       
        if($stock === null||empty($stock)){
            Response::error(404, "Stock record not found");
        }
        
        Response::success($stock, "Stock retrieved successfully");
    }

    public function adjustProductStock($stock_id){
        $id = RequestValidator::parseId($stock_id);
        $data = RequestValidator::validate([
            'stockId'      => 'required|integer',
            'qty'         => 'required|integer',
            'low_stock_threshold' => 'required|integer',           
        ]);

        $data = RequestValidator::sanitize($data);
        $updatedStock = ProductStockService::adjustProductStock($id, $data);
        if(!$updatedStock){
            Response::error(500, "Failed to update product stock.");
        }
        Response::success($updatedStock, "Product stock updated successfully.");
    }
    
    public function adjustCategoryStock($stock_id){
        $id = RequestValidator::parseId($stock_id);
        $data = RequestValidator::validate([
            'stockId'      => 'required|integer',
            'qty'         => 'required|integer',
            'low_stock_threshold' => 'required|integer',           
        ]);

        $data = RequestValidator::sanitize($data);
        $updatedStock = ProductStockService::adjustCategoryStock($id, $data);
        if(!$updatedStock){
            Response::error(500, "Failed to update category stock.");
        }
        Response::success($updatedStock, "Category stock updated successfully.");
    }


    
}

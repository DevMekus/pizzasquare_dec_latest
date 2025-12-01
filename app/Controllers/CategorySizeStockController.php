<?php

namespace App\Controllers;

use App\Services\CategorySizeStockService;
use App\Utils\Response;
use App\Utils\RequestValidator;

class CategorySizeStockController
{
    /** GET /c-stock */ 
    public function index()
    {
        $data = CategorySizeStockService::fetchAll();
        
        if ($data === false) {
             Response::error(400,"Failed to fetch category size stock data.");
        }

        if (empty($data)) {
             Response::error(404, "No category size stock data found.");
        }
        
        Response::success($data, "Category size stock data fetched successfully.");
    }

    /** GET /c-stock/{id} */
    public function show($id)
    {
        $id = RequestValidator::parseId($id);
        $data = CategorySizeStockService::fetchById($id);
        
        if ($data === false) {
             Response::error(400,"Failed to fetch category size stock data.");
        }

        if (empty($data)) {
             Response::error(404, "No category size stock data found for the given ID.");
        }
        
        Response::success($data, "Category size stock data fetched successfully.");
    }

    /** POST /c-stock */
    public function create(){
         $data = RequestValidator::validate([
            'category_id' => 'required|integer',
            'size_id'       => 'required|string',
            'qty'   => 'required|integer'
        ]);
        $data = RequestValidator::sanitize($data);
        $result = CategorySizeStockService::create($data);
        if ($result === false) {
             Response::error(400, "Failed to create category size stock entry.");
        }
        Response::success($result, "Category size stock entry created successfully.");

    }

    /** PUT /c-stock/{id} */
    public function update($id){
         $id = RequestValidator::parseId($id);
         $data = RequestValidator::validate([
            'category_id' => 'required|integer',
            'size_id'       => 'required|string',
            'qty'   => 'required|integer'
        ]);
        $data = RequestValidator::sanitize($data);
        $result = CategorySizeStockService::update($id, $data);
        if ($result === false) {
             Response::error(400, "Failed to update category size stock entry.");
        }
        Response::success($result, "Category size stock entry updated successfully.");
    }

    /** DELETE /c-stock/{id} */
    public function destroy($id){
         $id = RequestValidator::parseId($id);
         $result = CategorySizeStockService::delete($id);
         if ($result === false) {
             Response::error(400, "Failed to delete category size stock entry.");
        }
        Response::success($result, "Category size stock entry deleted successfully.");
    }

  
}

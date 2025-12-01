<?php

namespace App\Controllers;

use App\Services\ProductSizesService;
use App\Utils\Response;
use App\Utils\RequestValidator;
use App\Utils\Request;


class ProductSizesController
{
    /**
     * GET /product-sizes/{product_id}
     */
    public function getSizes($product_id)
    {
        $sizes = ProductSizesService::fetchByProductId($product_id);
        return Response::success($sizes, "Product sizes retrieved");
    }

   

    public function addSizesBulk()
    {
        $payload = Request::getBody();

        // If data is wrapped inside "data", decode it
        if (isset($payload['data']) && is_string($payload['data'])) {
            $payload = json_decode($payload['data'], true);
        }

        if (!is_array($payload) || empty($payload)) {
            Response::error(422, "Invalid data format. Expected an array.");
        }

        $validatedData = [];

        foreach ($payload as $index => $item) {
            $row = RequestValidator::validate([
                'product_id'    => 'required|integer',
                'size_id'       => 'required|integer',
                'price'         => 'required|integer',
                'shared_stock'  => 'required|integer'
            ], $item);

            $validatedData[] = RequestValidator::sanitize($row);
        }      

        $result = ProductSizesService::addBulkSizes($validatedData);

        if ($result) {
            Response::success([], "Product sizes added successfully.");
        }

        Response::error(500, "Failed to add product sizes.");
    }



    /**
     * PUT /product-sizes/{id}
     */
    public function updateSize($id)
    {
        $data = RequestValidator::sanitize($_POST);

        $result = ProductSizesService::updateSize($id, $data);

        if ($result) {
            return Response::success(null, "Size updated");
        }

        return Response::error(400, "Failed to update size");
    }

    /**
     * DELETE /product-sizes/{id}
     */
    public function deleteSize($id)
    {
        $result = ProductSizesService::deleteSize($id);

        if ($result) {
            return Response::success(null, "Size deleted");
        }

        return Response::error(400, "Failed to delete size");
    }
}

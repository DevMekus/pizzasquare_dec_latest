<?php

namespace App\Controllers;

use App\Services\SizesService;
use App\Utils\Response;
use App\Utils\RequestValidator;

class SizesController
{
    /**
     * GET /sizes
     */
    public function index()
    {
        try {
            $sizes = SizesService::fetchAll();

            if ($sizes === false) {
                 Response::error(500, "Error fetching sizes");
            }

            if (empty($sizes)) {
                 Response::success([], "No sizes found");
            }
            
            return Response::success($sizes, "Sizes retrieved successfully");
        } catch (\Throwable $th) {
            return Response::error(500, "Error fetching sizes");
        }
    }

    /**
     * GET /sizes/{id}
     */
    public function show($id)
    {
        $id = RequestValidator::parseId($id);
        $size = SizesService::fetchById($id);

        if ($size) {
            return Response::success($size, "Size retrieved successfully");
        }
        return Response::error(404, "Size not found");
    }

    /**
     * POST /sizes
     */
    public function store()
    {
        $data = RequestValidator::validate([
            'label' => 'required|string',
            'category_id' => 'required|integer',
            'ordering' => 'required|integer'
        ]);

        $data = RequestValidator::sanitize($data);       

        $size_id = SizesService::create($data);
        if ($size_id) {
             Response::success(["id"=>$size_id],'Size created successfully');
        }
         
        Response::error(400, "Failed to create size");
    }

    /**
     * PUT /sizes/{id}
     */
    public function update($id)
    {
        $id = RequestValidator::parseId($id);
        $data = RequestValidator::validate([
                'category_id' => 'required|integer',
        ]);
        $data = RequestValidator::sanitize($data);
        
        $size = SizesService::fetchById($id);

        if(empty($size))Response::error(404,"Size not found");

        $updated = SizesService::update($id, $data, $size[0]);
        if ($updated) {
             Response::success(["success"=>true, "message"=>"Size updated"]);
        }

         Response::error(["success"=>false, "message"=>"Failed to update size"], 400);
    }

    /**
     * DELETE /sizes/{id}
     */
    public function destroy($id)
    {
        $id = RequestValidator::parseId($id);

        $deleted = SizesService::delete($id);
        if ($deleted) {
            return Response::success([], "Size deleted successfully");
        }

        return Response::error(400, "Failed to delete size");
    }
}

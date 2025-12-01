<?php
namespace App\Controllers;

use App\Services\CategoryService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class CategoryController
{
    // GET /categories
    public function index()
    {
        try {
            $categories = CategoryService::fetchAll();
            if(empty($categories)) {
                Response::error(404, "No categories found");
            }
            Response::success($categories, "Categories fetched successfully");
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryController::index', [], $th);
            Response::error(500, "Failed to fetch categories");
        }
    }

    // GET /categories/{id}
    public function show($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $category = CategoryService::fetchById($id);

            if ($category) {
                Response::success($category, "Category fetched successfully");
            } else {
                Response::error(404, "Category not found");
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryController::show', ['id' => $id], $th);
            Response::error(500, "Failed to fetch category");
        }
    }

    // POST /categories
    public function store()
    {
        try {
            $data = RequestValidator::validate([
                'name' => 'required|string'
            ]);
            $data = RequestValidator::sanitize($data);

            $id = CategoryService::create($data);

            if ($id) {
                Response::success(['id' => $id], "Category created successfully");
            } else {
                Response::error(400, "Failed to create category");
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryController::store', $data ?? [], $th);
            Response::error(500, "Failed to create category");
        }
    }

    // PUT /categories/{id}
    public function update($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $data = RequestValidator::validate([
                'name' => 'required|string'
            ]);
            $data = RequestValidator::sanitize($data);

            $updated = CategoryService::update($id, $data);

            if ($updated) {
                Response::success(null, "Category updated successfully");
            } else {
                Response::error(400, "Failed to update category");
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryController::update', $data ?? [], $th);
            Response::error(500, "Failed to update category");
        }
    }

    // DELETE /categories/{id}
    public function destroy($id)
    {
        try {
            $id = RequestValidator::parseId($id);

            $deleted = CategoryService::delete($id);

            if ($deleted) {
                Response::success(null, "Category deleted successfully");
            } else {
                Response::error(400, "Failed to delete category");
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryController::destroy', ['id' => $id], $th);
            Response::error(500, "Failed to delete category");
        }
    }
}

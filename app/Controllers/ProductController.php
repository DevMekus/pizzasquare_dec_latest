<?php
namespace App\Controllers;

use App\Services\ProductService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class ProductController
{
    /** ----------------------
     *  GET ALL PRODUCTS
     *  ---------------------- */
    public function index()
    {
        try {
            $products = ProductService::fetchAll();

            if(empty($products)) {
                Response::error(404, "No products found");
            }

            Response::success($products, "Products retrieved successfully");

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::index', [], $th);
            Response::error(500, "Error fetching products");
        }
    }

    /** ----------------------
     *  GET A SINGLE PRODUCT
     *  ---------------------- */
    public function show($id)
    {
        try {
            $id = RequestValidator::parseId($id);

            $product = ProductService::fetchById($id);           

            if ($product) {
                Response::success($product, "Product retrieved");
            } else {
                Response::error(404, "Product not found");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::show', [], $th);
            Response::error(500, "Error retrieving product");
        }
    }

     /** ----------------------
     *  GET A SINGLE PRODUCT FULL DETAILS
     *  ---------------------- */
    public function showFull($id)
    {
        try {
            $id = RequestValidator::parseId($id);

            $product = ProductService::fetchFullProduct($id);            

            if ($product) {
                Response::success($product, "Product retrieved");
            } else {
                Response::error(404, "Product not found");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::showFull', [], $th);
            Response::error(500, "Error retrieving product");
        }
    }

    /** =========================
     *  GET PIZZAS WITH SIZES
     *  =========================*/
    public function pizzasWithSizes()
    {
        try {
            $data = ProductService::fetchPizzasWithSizes();

            if ($data === false) {
                return Response::error(500, "Unable to fetch pizza products");
            }

            if (empty($data)) {
                Response::error(404, "No pizza products found");
            }

             Response::success($data, "Pizza products with sizes loaded");
            
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::pizzasWithSizes', [], $th);
            Response::error(500, "Server error loading pizza products");
        }
    }
    

    /** ----------------------
     *  CREATE PRODUCT
     *  ---------------------- */
    public function store()
    {
        try {
            $required = ['name', 'category_id'];
            $data = RequestValidator::validate($required, $_POST);
            $data = RequestValidator::sanitize($data); 
            
            //check if image is uploaded
            if (!isset($_FILES['productImage']) || $_FILES['productImage']['error'] !== UPLOAD_ERR_OK) {
                Response::error(400, "Product image is required");
            }

            //check if product exists
            $existingProduct = ProductService::fetchById($data['name']);
            
            if ($existingProduct) {
                Response::error(409, "Product with this name already exists");
            }

          

            $productId = ProductService::create($data);

            if ($productId) {
                Response::success(['id' => $productId], "Product created");
            } else {
                Response::error(400, "Failed to create product");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::store', [], $th);
            Response::error(500, "Error creating product");
        }
    }

    /** ----------------------
     *  UPDATE PRODUCT
     *  ---------------------- */
    public function update($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $data = RequestValidator::sanitize($_POST);           
            
            $product = ProductService::fetchById($id);
            if (!$product) {
                Response::error(404, "Product not found");
            }          

            $updated = ProductService::update($id, $data, $product[0]);

            if ($updated) {
                Response::success([], "Product updated");
            } else {
                Response::error(400, "Failed to update product");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::update', [], $th);
            Response::error(500, "Error updating product");
        }
    }

    /** ----------------------
     *  DELETE PRODUCT
     *  ---------------------- */
    public function delete($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            
            
            $product = ProductService::fetchById($id);
            if (!$product) {
                Response::error(404, "Product not found");
            }

            $deleted = ProductService::delete($id, $product[0]);

            if ($deleted) {
                Response::success([], "Product deleted");
            } else {
                Response::error(400, "Failed to delete product");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::delete', [], $th);
            Response::error(500, "Error deleting product");
        }
    }

    public function getIngredients()
    {
        try {
            $ingredients = ProductService::fetchIngredients();

            if(empty($ingredients)) {
                Response::error(404, "No ingredients found");
            }

            Response::success($ingredients, "Ingredients retrieved successfully");

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::getIngredients', [], $th);
            Response::error(500, "Error fetching ingredients");
        }
    }

    public function addIngredient()
    {
        try {
            $required = ['ingredient_name', 'category_id'];
            $data = RequestValidator::validate($required);
            $data = RequestValidator::sanitize($data);
            
            //check if ingredient exists
            $existingIngredient = ProductService::fetchIngredientByNameId($data['ingredient_name'], $data['category_id']);
            
            if ($existingIngredient) {
                Response::error(409, "Ingredient with this name already exists");
            }

            $ingredientId = ProductService::addIngredient($data);

            if ($ingredientId) {
                Response::success(['id' => $ingredientId], "Ingredient created");
            } else {
                Response::error(400, "Failed to create ingredient");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::addIngredient', [], $th);
            Response::error(500, "Error creating ingredient");
        }
    }  
    
    public function updateIngredient($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $required = ['ingredient_name', 'category_id'];
            $data = RequestValidator::validate($required);       
            
            $ingredient = ProductService::fetchIngredientById($id);
            if (!$ingredient) {
                Response::error(404, "Ingredient not found");
            }          

            $updated = ProductService::editIngredient($id, $data, $ingredient);

            if ($updated) {
                Response::success([], "Ingredient updated");
            } else {
                Response::error(400, "Failed to update ingredient");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::updateIngredient', [], $th);
            Response::error(500, "Error updating ingredient");
        }
    }

    public function deleteIngredient($id)
    {
        try {
           
            $id = RequestValidator::parseId($id);            
           
            $ingredient = ProductService::fetchIngredientById($id);
           
            if (!$ingredient) {
                Response::error(404, "Ingredient not found");
            }

            $deleted = ProductService::removeIngredient($id, $ingredient);

            if ($deleted) {
                Response::success([], "Ingredient deleted");
            } else {
                Response::error(400, "Failed to delete ingredient");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::deleteIngredient', [], $th);
            Response::error(500, "Error deleting ingredient");
        }
    }

    public function assignIngredientsToProduct()
    {
        try {
            $required = ['product_id', 'ingredient_ids'];
            $data = RequestValidator::validate($required);
            $data = RequestValidator::sanitize($data);           
            
            $assigned = ProductService::assignIngredients($data['product_id'], $data['ingredient_ids']);

            if ($assigned) {
                Response::success([], "Ingredients assigned to product");
            } else {
                Response::error(400, "Failed to assign ingredients to product");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::assignIngredientsToProduct', [], $th);
            Response::error(500, "Error assigning ingredients to product");
        }
    }

    public function getIngredientsByProduct($id)
    {
        try {
            $id = RequestValidator::parseId($id);

            $ingredients = ProductService::fetchIngredientsByProductId($id);

            if(empty($ingredients)) {
                Response::error(404, "No ingredients found for this product");
            }

            Response::success($ingredients, "Ingredients for product retrieved successfully");

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::getIngredientsByProduct', [], $th);
            Response::error(500, "Error fetching ingredients for product");
        }
    }

    public function removeIngredientFromProduct($id)
    {
        try {
            $id = RequestValidator::parseId($id);           
           
            $removed = ProductService::unassignIngredientFromProduct($id);

            if ($removed) {
                Response::success([], "Ingredient removed from product");
            } else {
                Response::error(400, "Failed to remove ingredient from product");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductController::removeIngredientFromProduct', [], $th);
            Response::error(500, "Error removing ingredient from product");
        }
    }
}

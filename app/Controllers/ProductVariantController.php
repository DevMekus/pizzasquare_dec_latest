<?php

namespace App\Controllers;

use App\Services\ProductVariantService;
use App\Utils\Response; // your JSON response helper (create if missing)

class ProductVariantController
{
    private ProductVariantService $service;

    public function __construct()
    {
        $this->service = new ProductVariantService();
    }

    /** POST: /admin/product-variants/create */
    public function create()
    {
        $data = $_POST;

        $required = ["product_id", "variant_name", "price"];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return Response::error(400, "$field is required");
            }
        }

        $id = $this->service->createVariant($data);

        return Response::success([
            "variant_id" => $id
        ], "Variant created successfully");
    }

    /** GET: /admin/product-variants?product_id=3 */
    public function getByProduct()
    {
        $productId = $_GET["product_id"] ?? null;

        if (!$productId)
            Response::error(400, "Product ID required");
           

        $variants = $this->service->getProductVariants($productId);

        return Response::success($variants, "Variants retrieved successfully");
    }

    /** GET: /admin/product-variant/{id} */
    public function get($id)
    {
        $variant = $this->service->getVariant($id);

        if (!$variant)
            
            return Response::error(404, "Variant not found");

        return Response::success($variant, "Variant retrieved successfully");
    }

    /** POST: /admin/product-variants/update */
    public function update()
    {
        $id = $_POST["id"] ?? null;
        if (!$id)
            return Response::error(400, "ID required");

        unset($_POST["id"]);
        $this->service->updateVariant($id, $_POST);

        return Response::success(null, "Variant updated successfully");
    }

    /** DELETE: /admin/product-variants/delete/{id} */
    public function delete($id)
    {
        $this->service->deleteVariant($id);
        return Response::success(null, "Variant deleted successfully");
    }

    /** POST: /admin/product-variants/add-stock */
    public function addStock()
    {
        $id = $_POST["variant_id"];
        $qty = $_POST["qty"];

        $this->service->addStock($id, $qty);

        return Response::success(null, "Stock added successfully");
    }

    /** POST: /admin/product-variants/decrement-stock */
    public function decrementStock()
    {
        $id = $_POST["variant_id"];
        $qty = $_POST["qty"];

        $success = $this->service->decrementStock($id, $qty);

        if (!$success) {
            return Response::error(400, "Not enough stock");
        }

        return Response::success(null, "Stock decremented successfully");
    }

    /** POST: /admin/product-variants/toggle-active */
    public function toggleActive()
    {
        $id = $_POST["id"];
        $this->service->toggleActive($id);

        return Response::success(null, "Variant active status toggled successfully");
    }
}

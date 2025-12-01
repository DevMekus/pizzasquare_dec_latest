<?php

namespace App\Services;

use configs\Database;

class ProductVariantService
{
    private string $table = "product_variants";

    /** Create a product variant */
    public function createVariant(array $data)
    {
        return Database::insert($this->table, [
            "product_id"   => $data["product_id"],
            "variant_name" => $data["variant_name"],
            "price"        => $data["price"],
            "stock"        => $data["stock"] ?? 0,
            "sku"          => $data["sku"] ?? null,
            "is_active"    => $data["is_active"] ?? 1,
        ]);
    }

    /** Get all variants for a product */
    public function getProductVariants($productId)
    {
        return Database::all($this->table, [
            "product_id" => $productId
        ], [
            "order" => "id DESC"
        ]);
    }

    /** Get single variant */
    public function getVariant($id)
    {
        return Database::find($this->table, $id);
    }

    /** Update variant */
    public function updateVariant($variantId, array $data)
    {
        return Database::update($this->table, $data, [
            "id" => $variantId
        ]);
    }

    /** Delete variant */
    public function deleteVariant($variantId)
    {
        return Database::delete($this->table, [
            "id" => $variantId
        ]);
    }

    /** STOCK LOGIC – reduce stock when order is placed */
    public function decrementStock($variantId, $qty)
    {
        $variant = $this->getVariant($variantId);
        if (!$variant) return false;

        if ($variant["stock"] < $qty) {
            return false; // insufficient stock
        }

        $newStock = $variant["stock"] - $qty;

        return Database::update($this->table, [
            "stock" => $newStock
        ], [
            "id" => $variantId
        ]);
    }

    /** Add stock manually (for inventory) */
    public function addStock($variantId, $qty)
    {
        $variant = $this->getVariant($variantId);
        if (!$variant) return false;

        $newStock = $variant["stock"] + $qty;

        return Database::update($this->table, [
            "stock" => $newStock
        ], [
            "id" => $variantId
        ]);
    }

    /** Toggle active/inactive */
    public function toggleActive($variantId)
    {
        $v = $this->getVariant($variantId);
        return Database::update($this->table, [
            "is_active" => $v["is_active"] ? 0 : 1
        ], [
            "id" => $variantId
        ]);
    }
}

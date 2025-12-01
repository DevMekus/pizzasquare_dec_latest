<?php
namespace App\Services;

use configs\Database;
use App\Utils\Utility;
use App\Utils\Response;
use App\Services\ActivityService;

class StockService
{
    /**
     * Get available qty for a product_size variant (product_sizes.id).
     * Returns integer qty or false on error.
     */
    public static function getAvailableQtyByProductSizeId(int $productSizeId)
    {
        try {
            $product_sizes_tbl = Utility::$product_sizes; 
            $products_tbl = Utility::$products; 
            $category_size_stock_tbl = Utility::$category_size_stock; 
            $product_stock_tbl = Utility::$product_stock; 

            // fetch product_size row (includes product_id, size_id, uses_shared_stock)
            $ps = Database::joinTables(
                "$product_sizes_tbl ps",
                [
                    [
                        'type' => 'INNER',
                        'table' => $products_tbl . ' p',
                        'on' => 'p.id = ps.product_id'
                    ]
                ],
                [
                    'ps.id as id',
                    'ps.product_id as product_id',
                    'ps.size_id as size_id',
                    'ps.uses_shared_stock as uses_shared_stock',
                    'p.category_id as category_id'
                ],
                [
                    'ps.id' => $productSizeId
                ],
                [
                    'limit' => 1
                ]
            );

            if (empty($ps) || !isset($ps[0])) {
                return 0;
            }
            $ps = $ps[0];

            if ((int)$ps['uses_shared_stock'] === 1) {
                // shared stock row for category+size
                $row = Database::joinTables(
                    $category_size_stock_tbl . ' cs',
                    [],
                    ['cs.qty'],
                    [
                        'cs.category_id' => $ps['category_id'],
                        'cs.size_id' => $ps['size_id']
                    ],
                    ['limit' => 1]
                );
                return $row && isset($row[0]) ? (int)$row[0]['qty'] : 0;
            } else {
                $row = Database::joinTables(
                    $product_stock_tbl . ' psq',
                    [],
                    ['psq.qty'],
                    [
                        'psq.product_id' => $ps['product_id'],
                        'psq.size_id' => $ps['size_id']
                    ],
                    ['limit' => 1]
                );
                return $row && isset($row[0]) ? (int)$row[0]['qty'] : 0;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'StockService::getAvailableQtyByProductSizeId', ['product_size_id' => $productSizeId], $th);
            return false;
        }
    }

    /**
     * Decrement stock for a product_size (used during order commit).
     * - $productSizeId: id from product_sizes table
     * - $qty: positive integer to reduce
     * - $note: optional note inserted into stock_movements
     *
     * Returns true on success, or string error message on failure.
     */
    public static function decrementStockForProductSize(int $productSizeId, int $qty, ?string $note = null)
    {
        if ($qty <= 0) return "Invalid quantity";

        try {
            // Start transaction
            Database::beginTransaction();

            $product_sizes_tbl = Utility::$product_sizes;
            $products_tbl = Utility::$products;
            $category_size_stock_tbl = Utility::$category_size_stock;
            $product_stock_tbl = Utility::$product_stock;
            $stock_movements_tbl = Utility::$stock_movements;

            // fetch product_size + product.category_id FOR UPDATE (attempt lock)
            // NOTE: using raw SELECT FOR UPDATE via Database::query
            $selectSql = "SELECT ps.id, ps.product_id, ps.size_id, ps.uses_shared_stock, p.category_id
                          FROM {$product_sizes_tbl} ps
                          JOIN {$products_tbl} p ON p.id = ps.product_id
                          WHERE ps.id = :psid
                          LIMIT 1 FOR UPDATE";
            Database::query($selectSql, ['psid' => $productSizeId]);

            // Re-query the fetched row to read values
            $psRows = Database::joinTables(
                "{$product_sizes_tbl} ps",
                [
                    [
                        'type' => 'INNER',
                        'table' => $products_tbl . ' p',
                        'on' => 'p.id = ps.product_id'
                    ]
                ],
                ['ps.id', 'ps.product_id', 'ps.size_id', 'ps.uses_shared_stock', 'p.category_id'],
                ['ps.id' => $productSizeId],
                ['limit' => 1]
            );

            if (empty($psRows) || !isset($psRows[0])) {
                Database::rollBack();
                return "Variant not found";
            }

            $ps = $psRows[0];

            if ((int)$ps['uses_shared_stock'] === 1) {
                // lock the category_size_stock row with FOR UPDATE
                $selectCss = "SELECT id, qty FROM {$category_size_stock_tbl}
                              WHERE category_id = :cat AND size_id = :size
                              LIMIT 1 FOR UPDATE";
                Database::query($selectCss, ['cat' => $ps['category_id'], 'size' => $ps['size_id']]);

                // read the row
                $rows = Database::joinTables(
                    $category_size_stock_tbl . " cs",
                    [],
                    ['cs.id', 'cs.qty'],
                    [
                        'cs.category_id' => $ps['category_id'],
                        'cs.size_id' => $ps['size_id']
                    ],
                    ['limit' => 1]
                );

                if (empty($rows) || !isset($rows[0])) {
                    Database::rollBack();
                    return "Shared stock row missing for category-size";
                }

                $row = $rows[0];
                if ((int)$row['qty'] < $qty) {
                    Database::rollBack();
                    return "Insufficient stock";
                }

                // update qty
                $ok = Database::update($category_size_stock_tbl, ['qty' => $row['qty'] - $qty], ['id' => $row['id']]);
                if (!$ok) {
                    Database::rollBack();
                    return "Failed to update shared stock";
                }

                // insert movement
                $movementId = Database::insert($stock_movements_tbl, [
                    'movement_type' => 'sale',
                    'reference_type' => 'category_size',
                    'reference_id' => $row['id'],
                    'qty_change' => -$qty,
                    'note' => $note
                ]);

                // optional: log or use movementId

            } else {
                // product-level stock
                $selectPsq = "SELECT id, qty FROM {$product_stock_tbl}
                              WHERE product_id = :pid AND size_id = :size
                              LIMIT 1 FOR UPDATE";
                Database::query($selectPsq, ['pid' => $ps['product_id'], 'size' => $ps['size_id']]);

                $rows = Database::joinTables(
                    $product_stock_tbl . " psq",
                    [],
                    ['psq.id', 'psq.qty'],
                    [
                        'psq.product_id' => $ps['product_id'],
                        'psq.size_id' => $ps['size_id']
                    ],
                    ['limit' => 1]
                );

                if (empty($rows) || !isset($rows[0])) {
                    Database::rollBack();
                    return "Product stock row missing";
                }

                $row = $rows[0];
                if ((int)$row['qty'] < $qty) {
                    Database::rollBack();
                    return "Insufficient stock";
                }

                $ok = Database::update($product_stock_tbl, ['qty' => $row['qty'] - $qty], ['id' => $row['id']]);
                if (!$ok) {
                    Database::rollBack();
                    return "Failed to update product stock";
                }

                // insert movement
                $movementId = Database::insert($stock_movements_tbl, [
                    'movement_type' => 'sale',
                    'reference_type' => 'product_size',
                    'reference_id' => $row['id'],
                    'qty_change' => -$qty,
                    'note' => $note
                ]);
            }

            Database::commit();
            return true;
        } catch (\Throwable $th) {
            Database::rollBack();
            Utility::log($th->getMessage(), 'error', 'StockService::decrementStockForProductSize', ['product_size_id' => $productSizeId, 'qty' => $qty], $th);
            return "An error occurred while decrementing stock";
        }
    }

    /**
     * Add or subtract stock manually (admin adjustments).
     *
     * Expected $data keys:
     * - reference_type: 'category_size' | 'product_size'
     * - reference_id: id of category_size_stock OR product_stock (depending on type)
     * - change_quantity: integer (+ to add, - to subtract)
     * - reason: optional string
     * - created_by: optional user id
     *
     * Returns true or error string.
     */
    public static function adjustStock(array $data)
    {
        try {
            if (empty($data['reference_type']) || empty($data['reference_id']) || !isset($data['change_quantity'])) {
                return "Missing required fields";
            }

            $stock_movements_tbl = Utility::$stock_movements;

            Database::beginTransaction();

            $refType = $data['reference_type'];
            $refId = (int)$data['reference_id'];
            $change = (int)$data['change_quantity'];

            if ($refType === 'category_size') {
                $tbl = Utility::$category_size_stock;
            } elseif ($refType === 'product_size') {
                $tbl = Utility::$product_stock;
            } else {
                Database::rollBack();
                return "Invalid reference_type";
            }

            // fetch current row
            $row = Database::find($tbl, $refId, 'id');
            if (!$row) {
                Database::rollBack();
                return "Stock row not found";
            }

            $newQty = (int)$row['qty'] + $change;
            if ($newQty < 0) {
                Database::rollBack();
                return "Resulting quantity cannot be negative";
            }

            $ok = Database::update($tbl, ['qty' => $newQty], ['id' => $refId]);
            if (!$ok) {
                Database::rollBack();
                return "Failed to update stock";
            }

            // insert movement
            $movementId = Database::insert($stock_movements_tbl, [
                'movement_type' => $change > 0 ? 'restock' : 'manual_adjust',
                'reference_type' => $refType,
                'reference_id' => $refId,
                'qty_change' => $change,
                'note' => $data['reason'] ?? null
            ]);

            // activity
            ActivityService::saveActivity([
                'userid' => $data['created_by'] ?? ($_SESSION['userid'] ?? null),
                'type' => 'stock',
                'title' => 'stock adjusted',
            ]);

            Database::commit();
            return true;
        } catch (\Throwable $th) {
            Database::rollBack();
            Utility::log($th->getMessage(), 'error', 'StockService::adjustStock', $data, $th);
            return "An error occurred while adjusting stock";
        }
    }

    /**
     * Return low stock items (both category_size and product_stock) -- useful for admin alerts.
     * Returns array with two keys: category_size and product_size
     */
    public static function getLowStockItems(int $threshold = 5)
    {
        try {
            $category_size_stock_tbl = Utility::$category_size_stock;
            $product_stock_tbl = Utility::$product_stock;

            $catLow = Database::all($category_size_stock_tbl, ["qty <=" => $threshold]);
            $prodLow = Database::all($product_stock_tbl, ["qty <=" => $threshold]);

            return [
                'category_size' => $catLow,
                'product_size' => $prodLow
            ];
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'StockService::getLowStockItems', ['threshold' => $threshold], $th);
            return false;
        }
    }

    /**
     * Read stock movement logs (pagination optional)
     */
    public static function listStockMovements($limit = 50, $offset = 0)
    {
        try {
            $tbl = Utility::$stock_movements;
            $sql = "SELECT * FROM {$tbl} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            Database::query($sql, ['limit' => (int)$limit, 'offset' => (int)$offset]);

            // Database::query doesn't return rows, so use joinTables as fallback
            return Database::all($tbl, [], ['limit' => $limit]);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'StockService::listStockMovements', [], $th);
            return false;
        }
    }
}

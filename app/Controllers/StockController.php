<?php
namespace App\Controllers;

use App\Services\StockService;
use App\Utils\RequestValidator;
use App\Utils\Response;
use App\Utils\Utility;

class StockController
{
    /**
     * GET /admin/stock/variant/{product_size_id}
     * Returns available qty for a variant
     */
    public function getVariantQty($productSizeId)
    {
        try {
            $id = RequestValidator::parseId($productSizeId);
            $qty = StockService::getAvailableQtyByProductSizeId($id);
            if ($qty === false) {
                Response::error(500, "Error fetching quantity");
            } else {
                Response::success(['available_qty' => (int)$qty], "OK");
            }
        } catch (\InvalidArgumentException $e) {
            Utility::log($e->getMessage(), 'error', 'StockController::getVariantQty', ['id' => $productSizeId], $e);
            Response::error(400, $e->getMessage());
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'StockController::getVariantQty', ['id' => $productSizeId], $th);
            Response::error(500, "An error occurred");
        }
    }

    /**
     * POST /admin/stock/decrement
     * Body: product_size_id, qty, note (optional)
     * Used by order flow when committing items to stock.
     */
    public function decrementStock()
    {
        try {
            $required = ['product_size_id', 'qty'];
            $data = RequestValidator::validate($required, $_POST);
            $data = RequestValidator::sanitize($data);

            $productSizeId = RequestValidator::parseId($data['product_size_id']);
            $qty = (int)$data['qty'];
            $note = $_POST['note'] ?? null;

            $res = StockService::decrementStockForProductSize($productSizeId, $qty, $note);

            if ($res === true) {
                Response::success(null, "Stock decremented");
            } else {
                // service returns string message on failure
                Response::error(400, is_string($res) ? $res : "Failed to decrement stock");
            }

        } catch (\InvalidArgumentException $e) {
            Response::error(400, $e->getMessage());
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'StockController::decrementStock', $_POST, $th);
            Response::error(500, "An error occurred while decrementing stock");
        }
    }

    /**
     * POST /admin/stock/adjust
     * Body: reference_type (category_size|product_size), reference_id, change_quantity, reason (opt)
     * Admin manual adjustments.
     */
    public function adjustStock()
    {
        try {
            $required = ['reference_type', 'reference_id', 'change_quantity'];
            $data = RequestValidator::validate($required, $_POST);
            $data = RequestValidator::sanitize($data);

            // include optional fields
            $data['reason'] = $_POST['reason'] ?? null;
            $data['created_by'] = $_SESSION['userid'] ?? null;

            $res = StockService::adjustStock($data);

            if ($res === true) {
                Response::success(null, "Stock adjusted");
            } else {
                Response::error(400, is_string($res) ? $res : "Failed to adjust stock");
            }
        } catch (\InvalidArgumentException $e) {
            Response::error(400, $e->getMessage());
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'StockController::adjustStock', $_POST, $th);
            Response::error(500, "An error occurred while adjusting stock");
        }
    }

    /**
     * GET /admin/stock/low?threshold=5
     */
    public function lowStock()
    {
        try {
            $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 5;
            $items = StockService::getLowStockItems($threshold);
            if ($items === false) {
                Response::error(500, "Failed to fetch low stock items");
            } else {
                Response::success($items, "Low stock items");
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'StockController::lowStock', [], $th);
            Response::error(500, "An error occurred");
        }
    }

    /**
     * GET /admin/stock/movements?limit=50
     */
    public function movements()
    {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            $rows = StockService::listStockMovements($limit, $offset);
            if ($rows === false) {
                Response::error(500, "Failed to fetch stock movements");
            } else {
                Response::success($rows, "OK");
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'StockController::movements', [], $th);
            Response::error(500, "An error occurred");
        }
    }
}

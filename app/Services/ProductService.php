<?php
namespace App\Services;

use configs\Database;
use App\Utils\Utility;
use App\Utils\Response;
use App\Services\ProductSizesService;

class ProductService
{
    /** =========================
     *  FETCH ALL PRODUCTS
     *  =========================*/ 

    public static function fetchAll()
    {
        $categories = Utility::$categories;
        $products_tbl = Utility::$products;

        try {

            // Fetch products with category
            $products = Database::joinTables(
                "$products_tbl p",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$categories c",
                        "on"   => "p.category_id = c.id"
                    ],
                ],
                [
                    "p.*",
                    "c.id AS category_id",
                    "c.name AS category",
                ],
                [],
                [
                    "order" => "p.name ASC"
                ]
            );

            // CUSTOM PIZZA ORDER (same as fetchProducts)
            $customPizzaOrder = [
                'Mega Beef',
                'BBQ Chicken',
                'Hotdog',
                'Margherita',
                'Italian Xtra'
            ];

            // Split pizzas & non-pizzas
            $pizzas = [];
            $others = [];

            foreach ($products as $product) {
                if (strtolower($product['category']) === 'pizza') {
                    $pizzas[] = $product;
                } else {
                    $others[] = $product;
                }
            }

            // SORT PIZZAS BY CUSTOM ORDER
            usort($pizzas, function ($a, $b) use ($customPizzaOrder) {
                $posA = false;
                $posB = false;

                foreach ($customPizzaOrder as $index => $name) {
                    if (stripos($a['name'], $name) !== false) $posA = $index;
                    if (stripos($b['name'], $name) !== false) $posB = $index;
                }

                $posA = $posA === false ? PHP_INT_MAX : $posA;
                $posB = $posB === false ? PHP_INT_MAX : $posB;

                return $posA <=> $posB;
            });

            // MERGE BACK TOGETHER
            return array_merge($pizzas, $others);

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductService::fetchAll', [], $th);
            return false;
        }
    }


    /** =========================
     *  FETCH PRODUCT BY ID
     *  =========================*/   

    public static function fetchById($id)
    {
        $categories = Utility::$categories;
        $products_tbl = Utility::$products;
        $product_sizes = Utility::$product_sizes;

        try {

            // Fetch the matching products
                $products = Database::joinTables(
                "$products_tbl p",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$categories c",
                        "on"   => "p.category_id = c.id"
                    ],
                    [
                        "type" => "LEFT",
                        "table" => "$product_sizes ps",
                        "on"   => "p.id = ps.product_id"
                    ],
                ],
                [
                    "p.*",
                    "c.id AS category_id",
                    "c.name AS category",
                    "ps.id AS size_id",
                    "ps.price AS size_price",
                ],
                [
                    "OR" => [
                        "p.id"   => $id,
                        "p.name" => $id,
                        "p.sku"  => $id,
                    ]
                ],
                [
                    "order" => "p.name ASC"
                ]
            );

            // If nothing found
            if (!$products || count($products) === 0) {
                return [];
            }

            // CUSTOM PIZZA ORDER (same as other methods)
            $customPizzaOrder = [
                'Mega Beef',
                'BBQ Chicken',
                'Hotdog',
                'Margherita',
                'Italian Xtra'
            ];

            // Split pizzas & non-pizzas
            $pizzas = [];
            $others = [];

            foreach ($products as $product) {
                if (strtolower($product['category']) === 'pizza') {
                    $pizzas[] = $product;
                } else {
                    $others[] = $product;
                }
            }

            // SORT PIZZAS BY CUSTOM ORDER
            usort($pizzas, function ($a, $b) use ($customPizzaOrder) {
                $posA = false;
                $posB = false;

                foreach ($customPizzaOrder as $index => $name) {
                    if (stripos($a['name'], $name) !== false) $posA = $index;
                    if (stripos($b['name'], $name) !== false) $posB = $index;
                }

                $posA = ($posA === false ? PHP_INT_MAX : $posA);
                $posB = ($posB === false ? PHP_INT_MAX : $posB);

                return $posA <=> $posB;
            });

            // Merge pizzas (sorted) + others
            return array_merge($pizzas, $others);

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductService::fetchById', [], $th);
            return false;
        }
    }


    /** =========================
     *  FETCH Full Product DETAILS BY ID
     *  =========================*/
    public static function fetchFullProduct($productId)
    {
        $product = self::fetchById($productId);
        $sizes = ProductSizesService::fetchByProductId($productId);

        return [
            "product" => $product,
            "sizes"   => $sizes
        ];
    }


    /** =========================
     *  CREATE A NEW PRODUCT
     *  =========================*/
    public static function create($data)
    {
        try {
            $products_tbl = Utility::$products;

            $image = null;

            if (!empty($_FILES['productImage']['name']))
            {
                $target_dir = "public/UPLOADS/products/";
                $product_images = Utility::uploadDocuments('productImage', $target_dir);

                if (!$product_images || !$product_images['success']) {
                    return Response::error(500, "Image upload failed");
                }

                $image  = $product_images['files'][0];                
            } 

            $product = [
                'category_id'  => intval($data['category_id']),
                'name'         => $data['name'],
                'sku'          => Utility::generate_uniqueId(),              
                'description'  => $data['description'] ?? null,
                'image'        => $image,               
                'is_active'    => $data['is_active'] ?? 1,
            ];             

            $insertId = Database::insert($products_tbl, $product);

            if ($insertId) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type'   => 'product',
                    'title'  => 'new product added'
                ]);

                return $insertId;
            }

            return false;

        } catch (\Throwable $th) {
            Utility::log(
                $th->getMessage(),
                'error',
                'ProductService::create',
                ['data' => $data],
                $th
            );
            Response::error(500, "Error creating product");
        }
    }

    /** =========================
     *  UPDATE PRODUCT
     *  =========================*/
    public static function update($id, $data, $product)
    {
        try {
            $products_tbl = Utility::$products;
          

            Database::beginTransaction();

            $sizes = null;

            if (isset($data['sizes'])) {
                $raw = $data['sizes'];

                // Convert &quot; back to normal quotes
                $clean = html_entity_decode($raw, ENT_QUOTES);

                // Now decode JSON normally
                $sizes = json_decode($clean, true);               

                foreach ($sizes as $size) {
                    ProductSizesService::updateProductPrice($size['id'], $size);
                } 

                unset($data['sizes']);
            }
            
            $image = null;
            $file = $_FILES['productImage'] ?? null;
            $hasFile = $file &&
                    (
                        (is_array($file['name']) && !empty($file['name'][0]) && $file['error'][0] === UPLOAD_ERR_OK) ||
                        (!is_array($file['name']) && !empty($file['name']) && $file['error'] === UPLOAD_ERR_OK)
                    );

            if (
                $hasFile
            ) {
                $target_dir = "public/UPLOADS/products/";
                $product_images = Utility::uploadDocuments('productImage', $target_dir);

                if (!$product_images || !$product_images['success']) {
                    return Response::error(500, "Image upload failed");
                }

                $image  = $product_images['files'][0];    
                
                if (!empty($product['image'])) {                   
                    $filePath = __DIR__ . "/../../public/UPLOADS/products/" . basename($product['image']);
                    if (file_exists($filePath)) unlink($filePath);
                }
            } 

            $uproduct = [
                'category_id'  => isset($data['category_id']) ? intval($data['category_id']) : $product['category_id'],
                'name'         => isset($data['name']) ? $data['name'] : $product['name'],                  
                'description'  => isset($data['description']) ? $data['description'] : $product['description'],
                'image'        => isset($image) ? $image : $product['image'],               
                'is_active'    => isset($data['is_active']) ? intval($data['is_active']) : intval($product['is_active']),
            ];      



            if (Database::update($products_tbl, $uproduct, ['id' => $id])) {

                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type'   => 'product',
                    'title'  => 'product updated'
                ]);
                Database::commit();

                return true;
            }   
            
            Database::rollBack();

            return false;

        } catch (\Throwable $th) {
          
            Utility::log(
                $th->getMessage(),
                'error',
                'ProductService::update',
                ['product_id' => $id],
                $th
            );
            Response::error(500, "Error updating product");
        }
    }

    /** =========================
     *  DELETE PRODUCT
     *  =========================*/
    public static function delete($id, $product)
    {
        try {
            $products_tbl = Utility::$products;

             if (!empty($product['image'])) {                   
                    $filePath = __DIR__ . "/../../public/UPLOADS/products/" . basename($product['image']);
                    if (file_exists($filePath)) unlink($filePath);
                }

            if (Database::delete($products_tbl, ['id' => $id])) {

                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type'   => 'product',
                    'title'  => 'product deleted'
                ]);

                return true;
            }

            return false;

        } catch (\Throwable $th) {
            Utility::log(
                $th->getMessage(),
                'error',
                'ProductService::delete',
                ['product_id' => $id],
                $th
            );
            Response::error(500, "Error deleting product");
        }
    }

    /** =========================
     *  FETCH PIZZA WITH SIZES
     *  =========================*/
    public static function fetchPizzasWithSizes()
    {
        try {
            $categories = Utility::$categories;
            $products_tbl = Utility::$products;

            // 1. Fetch all pizza products
            $pizzas = Database::joinTables(
                "$products_tbl p",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$categories c",
                        "on"   => "p.category_id = c.id"
                    ],
                ],
                [
                    "p.*",
                    "c.id AS category_id",
                    "c.name AS category"
                ],
                [
                    "p.category_id" => "1"
                ],
                [
                    "order" => "p.name ASC"
                ]
            );

            if (!$pizzas) return [];

            $result = [];

            // 2. Attach sizes for each pizza
            foreach ($pizzas as $row) {
                $sizes = ProductSizesService::fetchByProductId($row['id']);

                $result[] = [
                    "id"         => $row["id"],
                    "name"       => $row["name"],
                    "sku"        => $row["sku"],
                    "description"=> $row["description"],
                    "image"      => $row["image"],
                    "category_id"=> $row["category_id"],
                    "category"   => $row["category"],
                    "is_active"  => $row["is_active"],
                    "sizes"      => $sizes
                ];
            }

            return $result;

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'ProductService::fetchPizzasWithSizes', [], $th);
            return false;
        }
    }

}

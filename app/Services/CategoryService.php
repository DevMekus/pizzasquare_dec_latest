<?php
namespace App\Services;

use configs\Database;
use App\Utils\Utility;
use App\Utils\Response;
use App\Services\ActivityService;

class CategoryService
{
    private static $table = 'categories';

    // Fetch all categories
    public static function fetchAll()
    {
        try {
            return Database::all(self::$table, [], ['order' => 'id ASC']);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryService::fetchAll', [], $th);
            return false;
        }
    }

    // Fetch category by ID
    public static function fetchById($id)
    {
        try {
            return Database::find(self::$table, $id);
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryService::fetchById', ['id' => $id], $th);
            return false;
        }
    }

    // Create a new category
    public static function create($data)
    {
        try {
            $category = [
                'name'       => $data['name'],
                'slug'       => strtolower(str_replace(' ', '-', $data['name'])),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $id = Database::insert(self::$table, $category);

            if ($id) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'] ?? null,
                    'type'   => 'category',
                    'title'  => "Category '{$data['name']}' created"
                ]);
                return $id;
            }
            return false;
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryService::create', $data, $th);
            return false;
        }
    }

    // Update existing category
    public static function update($id, $data)
    {
        try {
            $category = [
                'name'       => $data['name'],
                'slug'       => strtolower(str_replace(' ', '-', $data['name'])),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = Database::update(self::$table, $category, ['id' => $id]);

            if ($updated) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'] ?? null,
                    'type'   => 'category',
                    'title'  => "Category '{$data['name']}' updated"
                ]);
            }

            return $updated;
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryService::update', $data, $th);
            return false;
        }
    }

    // Delete category
    public static function delete($id)
    {
        try {
            $deleted = Database::delete(self::$table, ['id' => $id]);

            if ($deleted) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'] ?? null,
                    'type'   => 'category',
                    'title'  => "Category ID '{$id}' deleted"
                ]);
            }

            return $deleted;
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'CategoryService::delete', ['id' => $id], $th);
            return false;
        }
    }
}

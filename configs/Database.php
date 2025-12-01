<?php

namespace configs;

use PDO;
use PDOException;
use App\Utils\Utility;

class Database
{
    private static $pdo;

    private static function initialize()
    {

       if (!self::$pdo) {   
            $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
            try {
                self::$pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                Utility::log("DB Connection Failed: " . $e->getMessage(), 'error', 'DB::Constructor');
            }
        }
    }


    /** ---------------- CREATE ---------------- **/
    public static function insert($table, $data)
    {
        self::initialize();
        try {
            $fields = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($data);
            return self::$pdo->lastInsertId();     // UPDATED
        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::insert', ['host' => 'localhost'], $e);
            return false;
        }
    }
    
    public static function insertReturnId($table, $data)
    {
        // Same as insert(), kept for backward compatibility
        return self::insert($table, $data);
    }
  

    public static function getLastInsertId()
    {
        self::initialize();
        return self::$pdo->lastInsertId();
    }


    /** ---------------- READ ---------------- **/
    public static function find($table, $id, $idColumn = "id")
    {
        self::initialize();
        try {
            $sql = "SELECT * FROM {$table} WHERE {$idColumn} = :id LIMIT 1";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::find', ['host' => 'localhost'], $e);
        }
    }

    public static function all($table, $where = [], $options = [])
    {
        self::initialize();
        try {
            $sql = "SELECT * FROM {$table}";
            $params = [];

            if (!empty($where)) {
                $sql .= " WHERE " . self::buildWhere($where, $params);
            }

            if (!empty($options['order'])) {
                $sql .= " ORDER BY {$options['order']}";
            }

            if (!empty($options['limit'])) {
                $sql .= " LIMIT {$options['limit']}";
            }

            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::all', ['host' => 'localhost'], $e);
        }
    }

    /** ---------------- UPDATE ---------------- **/
    public static function update($table, $data, $where)
    {
        self::initialize();
        try {
            $set = implode(", ", array_map(fn($col) => "{$col} = :set_{$col}", array_keys($data)));
            $params = [];

            foreach ($data as $key => $value) {
                $params["set_" . $key] = $value;
            }
            $sql = "UPDATE {$table} SET {$set} WHERE " . self::buildWhere($where, $params);
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::update', ['host' => 'localhost'], $e);
        }
    }


    /** ---------------- DELETE ---------------- **/
    public static function delete($table, $where)
    {
        self::initialize();
        try {
            $params = [];
            $sql = "DELETE FROM {$table} WHERE " . self::buildWhere($where, $params);
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::delete', ['host' => 'localhost'], $e);
        }
    }

    /** ---------------- JOIN ---------------- **/
    public static function joinTables(
        $baseTable,
        $joins = [],
        $columns = ["*"],
        $where = [],
        $options = []
    ) {
        self::initialize();
        try {
            $cols = implode(", ", $columns);
            $sql = "SELECT {$cols} FROM {$baseTable}";
            $params = [];

            // Handle JOINs
            foreach ($joins as $join) {
                $type = strtoupper($join['type'] ?? 'INNER');
                $sql .= " {$type} JOIN {$join['table']} ON {$join['on']}";
            }

            // WHERE
            if (!empty($where)) {
                $sql .= " WHERE " . self::buildWhere($where, $params);
            }

            // GROUP BY
            if (!empty($options['group'])) {
                $sql .= " GROUP BY {$options['group']}";
            }

            // ORDER BY
            if (!empty($options['order'])) {
                $sql .= " ORDER BY {$options['order']}";
            }

            // LIMIT
            if (!empty($options['limit'])) {
                $sql .= " LIMIT {$options['limit']}";
            }

            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::joinTables', ['host' => 'localhost'], $e);
        }
    }


    /** ---------------- PAGINATION ---------------- **/
    public static function pagination($table, $page = 1, $perPage = 10, $where = [])
    {
        self::initialize();
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$table}";
            $params = [];
            if (!empty($where)) {
                $sql .= " WHERE " . self::buildWhere($where, $params);
            }

            $sql .= " LIMIT {$perPage} OFFSET {$offset}";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::pagination', ['host' => 'localhost'], $e);
        }
    }

    /** ---------------- TRANSACTIONS ---------------- **/
    public static function beginTransaction()
    {
        self::initialize();
        self::$pdo->beginTransaction();
    }

    public static function commit()
    {
        self::initialize();
        self::$pdo->commit();
    }

    public static function rollBack()
    {
        self::initialize();
        self::$pdo->rollBack();
    }

    public static function findWhere($table, $conditions = [])
    {
        self::initialize();

        try {
            $sql = "SELECT * FROM {$table} WHERE ";
            $params = [];

            foreach ($conditions as $column => $value) {
                $sql .= "{$column} = :{$column} AND ";
                $params[$column] = $value;
            }

            $sql = rtrim($sql, " AND ") . " LIMIT 1";

            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetch();

        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::findWhere', $conditions, $e);
            return false;
        }
    }


    /** ---------------- RAW QUERY ---------------- **/
    public static function query($sql, $params = [])
    {
        self::initialize();
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;     // UPDATED: return PDOStatement instead of boolean
        } catch (PDOException $e) {
            Utility::log($e->getMessage(), 'error', 'DB::query', ['host' => 'localhost'], $e);
            return false;
        }
    }

    /** ---------------- PRIVATE HELPERS ---------------- **/

    private static function buildWhere($where, &$params)
    {
        $clauses = [];

        foreach ($where as $key => $value) {
            // Handle OR conditions
            if (strtoupper($key) === 'OR' && is_array($value)) {
                $orParts = [];
                foreach ($value as $col => $val) {
                    $operator = '=';
                    $colName = $col;

                    // If operator is passed like "price >"
                    if (preg_match('/\s+(=|!=|>|<|>=|<=|LIKE|IN)$/i', $col, $matches)) {
                        $operator = strtoupper($matches[1]);
                        $colName = trim(str_replace($matches[0], '', $col));
                    }

                    $paramKey = str_replace('.', '_', $colName) . '_' . count($params);

                    if ($operator === 'IN' && is_array($val)) {
                        $placeholders = [];
                        foreach ($val as $i => $inVal) {
                            $ph = ":w_{$paramKey}_{$i}";
                            $params["w_" . $paramKey . "_{$i}"] = $inVal;
                            $placeholders[] = $ph;
                        }
                        $orParts[] = "{$colName} IN (" . implode(',', $placeholders) . ")";
                    } else {
                        $orParts[] = "{$colName} {$operator} :w_{$paramKey}";
                        $params["w_" . $paramKey] = $val;
                    }
                }
                $clauses[] = '(' . implode(' OR ', $orParts) . ')';
            } else {
                // Handle single condition
                $operator = '=';
                $colName = $key;

                if (preg_match('/\s+(=|!=|>|<|>=|<=|LIKE|IN)$/i', $key, $matches)) {
                    $operator = strtoupper($matches[1]);
                    $colName = trim(str_replace($matches[0], '', $key));
                }

                $paramKey = str_replace('.', '_', $colName) . '_' . count($params);

                if ($operator === 'IN' && is_array($value)) {
                    $placeholders = [];
                    foreach ($value as $i => $inVal) {
                        $ph = ":w_{$paramKey}_{$i}";
                        $params["w_" . $paramKey . "_{$i}"] = $inVal;
                        $placeholders[] = $ph;
                    }
                    $clauses[] = "{$colName} IN (" . implode(',', $placeholders) . ")";
                } else {
                    $clauses[] = "{$colName} {$operator} :w_{$paramKey}";
                    $params["w_" . $paramKey] = $value;
                }
            }
        }

        return implode(' AND ', $clauses);
    }
}

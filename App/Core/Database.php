<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;
use Exception;

class Database
{
    public static function connect(): PDO
    {
        try {
            return new PDO(
                sprintf(
                    "mysql:host=%s;dbname=%s;charset=utf8mb4",
                    $_ENV['DB_HOST'],
                    $_ENV['DB_NAME']
                ),
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]
            );

        } catch (PDOException $e) {
            throw new RuntimeException('Database connect failed: ' . $e->getMessage());
        }
    }

    public static function insert(string $table, array $attributes, array $fillable = [])
    {
        $pdo = self::connect();

        $data = self::fill($attributes, $fillable);

        if (empty($data)) {
            throw new Exception("Nenhum dado para inserir.");
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $params = array_values($data);

        $sql = "INSERT INTO {$table} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute($params)) {
            return (int)$pdo->lastInsertId();
        }

        return false;
    }

    public static function update(string $table, array $attributes, array $conditions = [], array $fillable = []): bool
    {
        $pdo = self::connect();

        $data = self::fill($attributes, $fillable);
        if (empty($data)) {
            throw new Exception("Nenhum campo para atualizar.");
        }

        $columns = [];
        $params = [];

        foreach ($data as $key => $value) {
            $columns[] = "$key = ?";
            $params[] = $value;
        }

        $where = [];
        foreach ($conditions as $key => $value) {
            $where[] = "$key = ?";
            $params[] = $value;
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $columns);
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function select(string $table, array $conditions = [], array $hidden = ['password']): array
    {
        $pdo = self::connect();

        $where = [];
        $params = [];

        if ($conditions) {
            foreach ($conditions as $key => $value) {
                $where[] = "$key = ?";
                $params[] = $value;
            }
        }

        $sql = "SELECT * FROM {$table}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        return self::filterHiddenFields($records, $hidden);
    }

    public static function delete(string $table, array $conditions = []): bool
    {
        $pdo = self::connect();

        $where = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $where[] = "$key = ?";
            $params[] = $value;
        }

        if (empty($where)) {
            throw new Exception("Nenhuma condição para deletar.");
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(" AND ", $where);

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    private static function filterHiddenFields(array $records, array $hidden = ['password']): array
    {
        return array_map(function ($record) use ($hidden) {
            foreach ($hidden as $field) {
                if (property_exists($record, $field)) {
                    unset($record->$field);
                }
            }
            return $record;
        }, $records);
    }

    private static function fill(array $attributes, array $fillable = []): array
    {
        return array_filter(
            $attributes,
            fn ($key) => in_array($key, $fillable, true),
            ARRAY_FILTER_USE_KEY
        );
    }
}

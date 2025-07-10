<?php

namespace App\Abstract;

use PDO;
use PDOException;
use App\Core\Database;

abstract class AbstractRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }


    public function findAll($table): array
    {
        $sql = "SELECT * FROM {$table}";
        $cursor = $this->pdo->prepare($sql);
        $cursor->execute();

        return $cursor->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllClause($clause, $table): array
    {
        if (!$clause || empty($clause)) {
            return [];
        }

        $where = implode(' AND ', $clause);
        $sql = "SELECT * FROM {table} WHERE {$where}";

        $cursor = $this->pdo->prepare($sql);
        $cursor->execute();

        return $cursor->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(string $table, array $data): int
    {
        if (!$data || empty($data)) {
            return 0;
        }

        $columns = implode(',', array_keys($data));
        $donnees = implode(',', array_map(fn($key) => ":{$key}", array_keys($data)));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$donnees})";

        $cursor = $this->pdo->prepare($sql);
        $cursor->execute($data);

        return (int)$this->pdo->lastInsertId();
    }
    

    public function findById(string $table, int $id): ?array
    {
        $sql = "SELECT * FROM $table WHERE id = :id";
        $cursor = $this->pdo->prepare($sql);
        $cursor->execute(['id' => $id]);
        $result = $cursor->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}



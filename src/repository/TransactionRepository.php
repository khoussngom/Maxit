<?php

namespace App\Repository;

use App\Entity\TransactionEntity;

class TransactionRepository
{
    private \PDO $pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function create(array $data): ?int
    {
        try {
            error_log("Création d'une transaction: " . json_encode($data));
            
            $columns = implode(', ', array_map(fn($key) => "\"$key\"", array_keys($data)));
            $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
            
            $sql = "INSERT INTO transactions ($columns) VALUES ($placeholders) RETURNING id";
            error_log("SQL: $sql");
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            error_log("Résultat de la création de la transaction: " . json_encode($result));
            
            return isset($result['id']) ? (int)$result['id'] : null;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la création d'une transaction: " . $e->getMessage());
            return null;
        }
    }
    
    public function findRecentByCompte(string $compteTelephone, int $limit = 10): array
    {
        try {
            $sql = "SELECT * FROM transactions 
                    WHERE compte_telephone = :compteTelephone 
                    ORDER BY date DESC, id DESC 
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':compteTelephone', $compteTelephone);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            $transactions = [];
            while ($transaction = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $transactions[] = new TransactionEntity($transaction);
            }
            
            return $transactions;
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    public function findRecentByPersonne($personneTelephone, int $limit = 10): array
    {
        try {

            $personneTelephone = (string) $personneTelephone;
            
            $sql = "SELECT t.* FROM transactions t
                    JOIN compte c ON t.compte_telephone = c.telephone
                    WHERE c.personne_telephone = :personneTelephone
                    ORDER BY t.date DESC, t.id DESC
                    LIMIT :limit";
        
            error_log("SQL transactions: $sql, personneTelephone: $personneTelephone, limit: $limit");
        
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':personneTelephone', $personneTelephone, \PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
        
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("Transactions trouvées: " . count($result));
            
            if (count($result) === 0) {
                error_log("Aucune transaction trouvée pour le téléphone: $personneTelephone");
            }

            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur SQL dans findRecentByPersonne: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }
    
    
    public function findAllByPersonneWithFilters($personneTelephone, array $filters = [], int $page = 1, int $perPage = 7): array
    {
        try {
            $personneTelephone = (string) $personneTelephone;
            
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT t.* FROM transactions t
                    JOIN compte c ON t.compte_telephone = c.telephone
                    WHERE c.personne_telephone = :personneTelephone";
            
            $params = ['personneTelephone' => $personneTelephone];
            
            if (!empty($filters['type'])) {
                $sql .= " AND t.type = :type";
                $params['type'] = $filters['type'];
            }
            
            if (!empty($filters['date'])) {

                $sql .= " AND DATE(t.date) = :date";
                $params['date'] = $filters['date'];
            }
            
            $countSql = str_replace("SELECT t.*", "SELECT COUNT(*) as total", $sql);
            $countStmt = $this->pdo->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStmt->bindValue(":$key", $value);
            }
            $countStmt->execute();
            $totalItems = (int) $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalItems / $perPage);
            
            $sql .= " ORDER BY t.date DESC, t.id DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return [
                'transactions' => $transactions,
                'pagination' => [
                    'total' => $totalItems,
                    'perPage' => $perPage,
                    'currentPage' => $page,
                    'totalPages' => $totalPages
                ]
            ];
        } catch (\PDOException $e) {
            error_log("Erreur SQL dans findAllByPersonneWithFilters: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'transactions' => [],
                'pagination' => [
                    'total' => 0,
                    'perPage' => $perPage,
                    'currentPage' => $page,
                    'totalPages' => 0
                ]
            ];
        }
    }
}
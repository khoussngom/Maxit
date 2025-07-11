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
    
    public function findRecentByPersonne(string $personneTelephone, int $limit = 10): array
    {
        try {
            $sql = "SELECT t.* FROM transactions t
                    JOIN compte c ON t.compte_telephone = c.telephone
                    WHERE c.personne_telephone = :personneTelephone
                    ORDER BY t.date DESC, t.id DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':personneTelephone', $personneTelephone);
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
}
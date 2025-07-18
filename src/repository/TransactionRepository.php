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
            

            $requiredFields = ['compte_telephone', 'montant', 'type'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    error_log("Champ obligatoire manquant: $field");
                    return null;
                }
            }
            
            if (!isset($data['date'])) {
                $data['date'] = date('Y-m-d');
            }
            
            $validColumns = ['id', 'montant', 'compte_telephone', 'type', 'date', 'motif', 'destination_telephone', 'source_telephone'];
            $filteredData = array_intersect_key($data, array_flip($validColumns));
            
            $columns = implode(', ', array_keys($filteredData));
            $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($filteredData)));
            
            $sql = "INSERT INTO transactions ($columns) VALUES ($placeholders) RETURNING id";
            error_log("SQL: $sql");
            error_log("Données filtrées: " . json_encode($filteredData));
            
            try {
                $stmt = $this->pdo->prepare($sql);
                
                foreach ($filteredData as $key => $value) {
                    if ($key === 'date') {
                        if (!is_string($value)) {
                            $value = date('Y-m-d');
                        }
                    } elseif ($key === 'montant') {
                        $value = (float)$value;
                    } elseif ($key === 'type') {
                        if (!in_array($value, ['depot', 'retrait', 'paiement', 'transfert'])) {
                            error_log("Type de transaction invalide: $value. Utilisation de 'depot' par défaut.");
                            $value = 'depot';
                        }
                    }
                    
                    $stmt->bindValue(":$key", $value);
                }
                
                $success = $stmt->execute();
                
                if (!$success) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Erreur d'exécution SQL: " . json_encode($errorInfo));
                    return null;
                }
                
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                error_log("Résultat de la création de la transaction: " . json_encode($result));
                
                if (!isset($result['id'])) {
                    $checkSql = "SELECT MAX(id) as last_id FROM transactions WHERE compte_telephone = :compte_telephone AND montant = :montant";
                    $checkStmt = $this->pdo->prepare($checkSql);
                    $checkStmt->bindValue(':compte_telephone', $filteredData['compte_telephone']);
                    $checkStmt->bindValue(':montant', (float)$filteredData['montant']);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if (isset($checkResult['last_id'])) {
                        error_log("ID récupéré par requête alternative: " . $checkResult['last_id']);
                        return (int)$checkResult['last_id'];
                    }
                    
                    $lastId = $this->pdo->lastInsertId();
                    if ($lastId) {
                        error_log("Dernier ID inséré: " . $lastId);
                        return (int)$lastId;
                    }
                    
                    error_log("Impossible de récupérer l'ID, mais la transaction a peut-être été créée");
                    return 1;
                }
                
                return (int)$result['id'];
            } catch (\PDOException $e) {
                error_log("Exception PDO lors de l'exécution de la requête: " . $e->getMessage());
                error_log("Code d'erreur: " . $e->getCode());
                
                if (strpos($e->getMessage(), 'invalid input value for enum') !== false) {
                    error_log("Problème avec le type enum, tentative d'insertion avec le type 'depot'");
                    $filteredData['type'] = 'depot';
                    
                    try {
                        $stmt = $this->pdo->prepare($sql);
                        foreach ($filteredData as $key => $value) {
                            $stmt->bindValue(":$key", $value);
                        }
                        $stmt->execute();
                        
                        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if (isset($result['id'])) {
                            return (int)$result['id'];
                        }
                    } catch (\Exception $e2) {
                        error_log("Échec de la seconde tentative avec type 'depot': " . $e2->getMessage());
                    }
                }
                
                return null;
            }
        } catch (\Exception $e) {
            error_log("Exception générale lors de la création d'une transaction: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return null;
        }
    }
    

    private function createTransactionsTable(): void
    {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS transactions (
                    id SERIAL PRIMARY KEY,
                    compte_telephone VARCHAR(20) NOT NULL,
                    montant DOUBLE PRECISION NOT NULL,
                    type VARCHAR(20) NOT NULL,
                    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    motif TEXT,
                    destination_telephone VARCHAR(20),
                    source_telephone VARCHAR(20)
                )
            ";
            
            $this->pdo->exec($sql);
            error_log("Table transactions créée avec succès!");
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de la table transactions: " . $e->getMessage());
        }
    }
    
    private function getTableName(): string
    {
        try {

            $stmt = $this->pdo->query("SELECT to_regclass('public.transactions')");
            $result = $stmt->fetchColumn();
            
            if ($result) {
                return 'transactions';
            }
            

            $variants = ['transaction', 'public.transaction', 'Transaction', 'TRANSACTIONS'];
            foreach ($variants as $variant) {
                $stmt = $this->pdo->query("SELECT to_regclass('$variant')");
                $result = $stmt->fetchColumn();
                
                if ($result) {
                    return $variant;
                }
            }
            

            return 'transactions';
        } catch (\Exception $e) {
            error_log("Erreur lors de la recherche du nom de la table: " . $e->getMessage());
            return 'transactions';
        }
    }
    

    private function logTableColumns(string $tableName): void
    {
        try {
            $stmt = $this->pdo->query("
                SELECT column_name, data_type, is_nullable 
                FROM information_schema.columns 
                WHERE table_name = '$tableName'
                ORDER BY ordinal_position
            ");
            
            $columns = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $columns[] = $row;
            }
            
            error_log("Colonnes de la table $tableName: " . json_encode($columns));
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des colonnes: " . $e->getMessage());
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
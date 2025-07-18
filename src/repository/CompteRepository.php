<?php

namespace App\Repository;

use App\Entity\TypeCompte;
use App\Entity\CompteEntity;
use App\Abstract\AbstractRepository;

class CompteRepository extends AbstractRepository
{
    public function findByPersonne($personneId): array
    {
        try {
            $personneId = (string) $personneId;
            
            error_log("Recherche des comptes pour la personne avec ID/téléphone: " . $personneId);
            

            try {
                $columnsStmt = $this->pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'compte' ORDER BY ordinal_position");
                $columns = $columnsStmt->fetchAll(\PDO::FETCH_COLUMN);
                error_log("Colonnes de la table compte: " . implode(", ", $columns));
            } catch (\Exception $e) {
                error_log("Erreur lors de la récupération des colonnes: " . $e->getMessage());
            }
            
            $sql = 'SELECT * FROM compte WHERE "personne_telephone" = :personneId';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['personneId' => $personneId]);
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("Nombre de comptes trouvés: " . count($results));
            

            if (!empty($results)) {
                error_log("Premier compte trouvé - clés: " . implode(", ", array_keys($results[0])));
                error_log("Premier compte trouvé - données: " . json_encode($results[0]));
            }
            
            return $results;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des comptes : " . $e->getMessage());
            return [];
        }
    }
    
    public function findByType(string $personneId, string $typeCompte): array
    {
        try {
            $personneId = (string) $personneId;
            
            error_log("Recherche des comptes de type '$typeCompte' pour la personne: " . $personneId);
            
            $sql = 'SELECT * FROM compte WHERE "personne_telephone" = :personneId AND "typecompte" = :typeCompte';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'personneId' => $personneId,
                'typeCompte' => $typeCompte
            ]);
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("Nombre de comptes trouvés: " . count($results));
            
            return $results;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des comptes par type : " . $e->getMessage());
            return [];
        }
    }
    
    public function findByTelephone(string $telephone): ?array
    {
        try {
            error_log("CompteRepository::findByTelephone - Recherche du compte avec téléphone: $telephone");
            
            $sql = 'SELECT * FROM compte WHERE "telephone" = :telephone';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['telephone' => $telephone]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("CompteRepository::findByTelephone - Compte trouvé avec correspondance exacte: " . json_encode($result));
                return $result;
            } 
            
            $formattedTel = preg_replace('/[^0-9]/', '', $telephone);
            if ($formattedTel !== $telephone) {
                error_log("CompteRepository::findByTelephone - Tentative avec numéro formaté: $formattedTel");
                
                $sql = 'SELECT * FROM compte WHERE "telephone" = :telephone';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['telephone' => $formattedTel]);
                
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($result) {
                    error_log("CompteRepository::findByTelephone - Compte trouvé avec numéro formaté: " . json_encode($result));
                    return $result;
                }
            }
            
            if (strlen($formattedTel) > 9) {
                $nationalFormat = substr($formattedTel, -9);
                error_log("CompteRepository::findByTelephone - Tentative avec format national (9 chiffres): $nationalFormat");
                
                $sql = 'SELECT * FROM compte WHERE "telephone" = :telephone OR "telephone" LIKE :pattern';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'telephone' => $nationalFormat,
                    'pattern' => '%' . $nationalFormat
                ]);
                
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($result) {
                    error_log("CompteRepository::findByTelephone - Compte trouvé avec format national: " . json_encode($result));
                    return $result;
                }
            }
            
            if (strlen($formattedTel) > 1 && $formattedTel[0] === '7') {
                $withCNoFirst7 = 'C' . substr($formattedTel, 1);
                error_log("CompteRepository::findByTelephone - Tentative en remplaçant le premier 7 par C: $withCNoFirst7");
                
                $sql = 'SELECT * FROM compte WHERE "telephone" = :telephone';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['telephone' => $withCNoFirst7]);
                
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($result) {
                    error_log("CompteRepository::findByTelephone - Compte trouvé avec format C + numéro sans premier 7: " . json_encode($result));
                    return $result;
                }
            }
            

            $withCPrefix = 'C' . $telephone;
            error_log("CompteRepository::findByTelephone - Tentative avec préfixe C: $withCPrefix");
            
            $sql = 'SELECT * FROM compte WHERE "telephone" = :telephone';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['telephone' => $withCPrefix]);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("CompteRepository::findByTelephone - Compte trouvé avec préfixe C: " . json_encode($result));
                return $result;
            }
            
            error_log("CompteRepository::findByTelephone - Aucun compte trouvé avec ce numéro après différentes tentatives");
            
            $debugSql = 'SELECT COUNT(*) as count FROM compte';
            $debugStmt = $this->pdo->prepare($debugSql);
            $debugStmt->execute();
            $totalCount = $debugStmt->fetch(\PDO::FETCH_ASSOC)['count'];
            error_log("CompteRepository::findByTelephone - Nombre total de comptes dans la base: $totalCount");
            
            $sampleSql = 'SELECT "telephone" FROM compte LIMIT 5';
            $sampleStmt = $this->pdo->prepare($sampleSql);
            $sampleStmt->execute();
            $samples = $sampleStmt->fetchAll(\PDO::FETCH_COLUMN);
            error_log("CompteRepository::findByTelephone - Exemples de numéros dans la base: " . implode(", ", $samples));
            
            return null;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du compte : " . $e->getMessage());
            return null;
        }
    }
    
    public function create(array $data): ?string
    {
        try {
            error_log("Tentative de création d'un compte: " . json_encode($data));
            
            $columns = implode(', ', array_map(fn($key) => "\"$key\"", array_keys($data)));
            $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
            
            $sql = "INSERT INTO compte ($columns) VALUES ($placeholders) RETURNING \"telephone\"";
            error_log("SQL: $sql");
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            error_log("Résultat de la création du compte: " . json_encode($result));
            
            return $result['telephone'] ?? null;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la création d'un compte : " . $e->getMessage());
            return null;
        }
    }
    
    public function updateSolde(string $telephone, float $nouveauSolde): bool
    {
        try {
            $sql = 'UPDATE compte SET "solde" = :solde WHERE "telephone" = :telephone';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'telephone' => $telephone,
                'solde' => $nouveauSolde
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du solde : " . $e->getMessage());
            return false;
        }
    }
    
    public function updateTypeCompte(string $telephone, string $nouveauType): bool
    {
        try {
            error_log("Tentative de mise à jour du type de compte $telephone vers $nouveauType");
            
            $sql = 'UPDATE compte SET "typecompte" = :typecompte WHERE "telephone" = :telephone';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'telephone' => $telephone,
                'typecompte' => $nouveauType
            ]);
            
            $success = $stmt->rowCount() > 0;
            error_log($success ? "Type de compte mis à jour avec succès" : "Échec de la mise à jour du type de compte");
            
            return $success;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du type de compte : " . $e->getMessage());
            return false;
        }
    }
}
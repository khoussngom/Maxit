<?php

namespace App\Services;

use App\Entity\TransactionEntity;
use App\Repository\TransactionRepository;

class TransactionService
{
    private TransactionRepository $transactionRepository;
    
    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }
    
    public function getTransactionsByPersonne(string $personneTelephone, int $limit = 10): array
    {
        return $this->transactionRepository->findRecentByPersonne($personneTelephone, $limit);
    }
    
    public function getTransactionsByCompte(string $compteTelephone, int $limit = 10): array
    {
        return $this->transactionRepository->findRecentByCompte($compteTelephone, $limit);
    }
    
    public function getAllTransactionsByPersonne(string $personneTelephone, array $filters = [], int $page = 1, int $perPage = 7): array
    {
        return $this->transactionRepository->findAllByPersonneWithFilters($personneTelephone, $filters, $page, $perPage);
    }
    
    public function createTransaction(string $compteTelephone, float $montant, string $type, string $motif = '', array $additionalData = []): bool
    {
        try {
            if (!in_array($type, ['depot', 'retrait', 'paiement', 'transfert'])) {
                throw new \InvalidArgumentException("Type de transaction invalide");
            }
            
            error_log("Préparation des données pour création de transaction - Compte: {$compteTelephone}, Montant: {$montant}, Type: {$type}");
            

            $data = [
                'compte_telephone' => $compteTelephone,
                'montant' => $montant,
                'type' => $type,
                'date' => date('Y-m-d'),
                'motif' => $motif ?: null
            ];
            
            error_log("Données de transaction: " . json_encode($data));

            if (!empty($additionalData)) {
                $data = array_merge($data, $additionalData);
                error_log("Données additionnelles: " . json_encode($additionalData));
            }
            
            $transactionId = $this->transactionRepository->create($data);
            error_log("Résultat de la création: ID=" . ($transactionId ?: 'null'));
            
            return $transactionId !== null;
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de la transaction: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    public function createTransfertTransaction(string $compteSourceTel, string $compteDestTel, float $montant, string $motif = ''): bool
    {
        try {

            $retraitOk = $this->createTransaction(
                $compteSourceTel, 
                $montant, 
                'transfert', 
                $motif, 
                ['destination_telephone' => $compteDestTel]
            );
            

            $depotOk = $this->createTransaction(
                $compteDestTel, 
                $montant, 
                'depot', 
                $motif, 
                ['source_telephone' => $compteSourceTel]
            );
            
            return $retraitOk && $depotOk;
        } catch (\Exception $e) {
            error_log("Erreur lors de la création des transactions de transfert: " . $e->getMessage());
            return false;
        }
    }
    
    public function effectuerDepot(string $compteTelephone, float $montant, string $motif = ''): bool
    {
        $transactionStarted = false;
        
        try {
            if ($montant <= 0) {
                throw new \InvalidArgumentException("Le montant du dépôt doit être positif");
            }
            
            $app = \App\Core\App::getInstance();
            $compteRepository = $app->getDependency('compteRepository');
            
            $compte = $compteRepository->findByTelephone($compteTelephone);
            if (!$compte) {
                throw new \Exception("Compte non trouvé");
            }
            

            if (method_exists($compteRepository, 'beginTransaction')) {
                $compteRepository->beginTransaction();
                $transactionStarted = true;
                error_log("Transaction BD démarrée pour dépôt");
            }
            
            try {

                $nouveauSolde = (float)$compte['solde'] + $montant;
                $soldeUpdated = $compteRepository->updateSolde($compteTelephone, $nouveauSolde);
                
                if (!$soldeUpdated) {
                    throw new \Exception("Erreur lors de la mise à jour du solde");
                }
                

                $transactionCreated = $this->createTransaction($compteTelephone, $montant, 'depot', $motif);
                
                if (!$transactionCreated) {
                    throw new \Exception("Erreur lors de l'enregistrement de la transaction");
                }
                

                if ($transactionStarted && method_exists($compteRepository, 'commit')) {
                    $compteRepository->commit();
                    error_log("Transaction BD validée pour dépôt");
                }
                
                return true;
            } catch (\Exception $e) {

                if ($transactionStarted && method_exists($compteRepository, 'rollBack')) {
                    $compteRepository->rollBack();
                    error_log("Transaction BD annulée pour dépôt: " . $e->getMessage());
                }
                throw $e;
            }
        } catch (\Exception $e) {
            error_log("Erreur lors du dépôt: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    public function effectuerRetrait(string $compteTelephone, float $montant, string $motif = ''): bool
    {
        $transactionStarted = false;
        
        try {
            if ($montant <= 0) {
                throw new \InvalidArgumentException("Le montant du retrait doit être positif");
            }
            
            $app = \App\Core\App::getInstance();
            $compteRepository = $app->getDependency('compteRepository');
            
            $compte = $compteRepository->findByTelephone($compteTelephone);
            if (!$compte) {
                throw new \Exception("Compte non trouvé");
            }
            
            if ((float)$compte['solde'] < $montant) {
                throw new \Exception("Solde insuffisant pour effectuer ce retrait");
            }
            

            if (method_exists($compteRepository, 'beginTransaction')) {
                $compteRepository->beginTransaction();
                $transactionStarted = true;
                error_log("Transaction BD démarrée pour retrait");
            }
            
            try {

                $nouveauSolde = (float)$compte['solde'] - $montant;
                $soldeUpdated = $compteRepository->updateSolde($compteTelephone, $nouveauSolde);
                
                if (!$soldeUpdated) {
                    throw new \Exception("Erreur lors de la mise à jour du solde");
                }
                

                $transactionCreated = $this->createTransaction($compteTelephone, $montant, 'retrait', $motif);
                
                if (!$transactionCreated) {
                    throw new \Exception("Erreur lors de l'enregistrement de la transaction");
                }
                

                if ($transactionStarted && method_exists($compteRepository, 'commit')) {
                    $compteRepository->commit();
                    error_log("Transaction BD validée pour retrait");
                }
                
                return true;
            } catch (\Exception $e) {

                if ($transactionStarted && method_exists($compteRepository, 'rollBack')) {
                    $compteRepository->rollBack();
                    error_log("Transaction BD annulée pour retrait: " . $e->getMessage());
                }
                throw $e;
            }
        } catch (\Exception $e) {
            error_log("Erreur lors du retrait: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }
    

    public function isComptePrincipal(string $compteTelephone): bool
    {
        try {
            $app = \App\Core\App::getInstance();
            $compteRepository = $app->getDependency('compteRepository');
            
            $compte = $compteRepository->findByTelephone($compteTelephone);
            if (!$compte) {
                return false;
            }
            

            return isset($compte['typecompte']) && $compte['typecompte'] === 'principal';
        } catch (\Exception $e) {
            error_log("Erreur lors de la vérification du compte principal: " . $e->getMessage());
            return false;
        }
    }

    
    public function getComptePrincipalByPersonne(string $personneTelephone): ?array
    {
        try {
            $app = \App\Core\App::getInstance();
            $compteRepository = $app->getDependency('compteRepository');
            
            $comptes = $compteRepository->findByPersonne($personneTelephone);
            
            foreach ($comptes as $compte) {
                if (isset($compte['typecompte']) && $compte['typecompte'] === 'principal') {
                    return $compte;
                }
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération du compte principal: " . $e->getMessage());
            return null;
        }
    }
    
    public function effectuerTransfert(string $compteSourceTel, string $compteDestTel, float $montant, string $motif = ''): bool
    {
        $transactionStarted = false;
        
        try {
            if ($montant <= 0) {
                throw new \InvalidArgumentException("Le montant du transfert doit être positif");
            }
            
            if ($compteSourceTel === $compteDestTel) {
                throw new \InvalidArgumentException("Impossible de faire un transfert vers le même compte");
            }
            
            $app = \App\Core\App::getInstance();
            $compteRepository = $app->getDependency('compteRepository');
            
            error_log("Démarrage du transfert de {$compteSourceTel} vers {$compteDestTel} pour un montant de {$montant}");
            
            $compteSource = $compteRepository->findByTelephone($compteSourceTel);
            if (!$compteSource) {
                throw new \Exception("Compte source non trouvé");
            }
            error_log("Compte source trouvé: " . json_encode($compteSource));
            
            $compteDest = $compteRepository->findByTelephone($compteDestTel);
            if (!$compteDest) {
                throw new \Exception("Compte destinataire non trouvé");
            }
            error_log("Compte destinataire trouvé: " . json_encode($compteDest));
            
            if ((float)$compteSource['solde'] < $montant) {
                throw new \Exception("Solde insuffisant pour effectuer le transfert");
            }
            
            if (method_exists($compteRepository, 'beginTransaction')) {
                $compteRepository->beginTransaction();
                $transactionStarted = true;
                error_log("Transaction BD démarrée avec compteRepository");
            }
            
            try {
                error_log("Création des enregistrements de transaction...");
                
                $retraitOk = $this->createTransaction(
                    $compteSourceTel, 
                    $montant, 
                    'transfert', 
                    $motif, 
                    ['destination_telephone' => $compteDestTel]
                );
                
                if (!$retraitOk) {
                    error_log("ERREUR: Échec de la création de la transaction de retrait");
                    throw new \Exception("Échec de la création de la transaction de retrait");
                }
                
                error_log("Transaction de retrait créée avec succès");
                
                $depotOk = $this->createTransaction(
                    $compteDestTel, 
                    $montant, 
                    'depot', 
                    $motif, 
                    ['source_telephone' => $compteSourceTel]
                );
                
                if (!$depotOk) {
                    error_log("ERREUR: Échec de la création de la transaction de dépôt");
                    throw new \Exception("Échec de la création de la transaction de dépôt");
                }
                
                error_log("Transaction de dépôt créée avec succès");
                
                error_log("Mise à jour des soldes...");
                $nouveauSoldeSource = (float)$compteSource['solde'] - $montant;
                $nouveauSoldeDest = (float)$compteDest['solde'] + $montant;
                
                $updateSoldeSource = $compteRepository->updateSolde($compteSourceTel, $nouveauSoldeSource);
                if (!$updateSoldeSource) {
                    error_log("ERREUR: Échec de la mise à jour du solde source");
                    throw new \Exception("Erreur lors de la mise à jour du solde source");
                }
                
                $updateSoldeDest = $compteRepository->updateSolde($compteDestTel, $nouveauSoldeDest);
                if (!$updateSoldeDest) {
                    error_log("ERREUR: Échec de la mise à jour du solde destinataire");
                    throw new \Exception("Erreur lors de la mise à jour du solde destinataire");
                }
                
                error_log("Soldes mis à jour avec succès.");
                
                if ($transactionStarted && method_exists($compteRepository, 'commit')) {
                    $result = $compteRepository->commit();
                    error_log("Transaction BD validée: " . ($result ? 'Succès' : 'Échec'));
                    if (!$result) {
                        throw new \Exception("Erreur lors de la validation de la transaction");
                    }
                }
                
                error_log("Transfert effectué avec succès!");
                return true;
            } catch (\Exception $e) {
                if ($transactionStarted && method_exists($compteRepository, 'rollBack')) {
                    $compteRepository->rollBack();
                    error_log("Transaction BD annulée: " . $e->getMessage());
                }
                throw $e;
            }
        } catch (\Exception $e) {
            error_log("Erreur lors du transfert: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }
}

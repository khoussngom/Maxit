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
            
            $data = [
                'compte_telephone' => $compteTelephone,
                'montant' => $montant,
                'type' => $type,
                'date' => date('Y-m-d H:i:s'),
                'motif' => $motif
            ];
            

            if (!empty($additionalData)) {
                $data = array_merge($data, $additionalData);
            }
            
            $transactionId = $this->transactionRepository->create($data);
            
            return $transactionId !== null;
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de la transaction: " . $e->getMessage());
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
            

            $nouveauSolde = (float)$compte['solde'] + $montant;
            $compteRepository->updateSolde($compteTelephone, $nouveauSolde);
            

            $transactionCreated = $this->createTransaction($compteTelephone, $montant, 'depot', $motif);
            
            return $transactionCreated;
        } catch (\Exception $e) {
            error_log("Erreur lors du dépôt: " . $e->getMessage());
            return false;
        }
    }
    
    public function effectuerRetrait(string $compteTelephone, float $montant, string $motif = ''): bool
    {
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
                throw new \Exception("Solde insuffisant");
            }
            

            $nouveauSolde = (float)$compte['solde'] - $montant;
            $compteRepository->updateSolde($compteTelephone, $nouveauSolde);
            

            $transactionCreated = $this->createTransaction($compteTelephone, $montant, 'retrait', $motif);
            
            return $transactionCreated;
        } catch (\Exception $e) {
            error_log("Erreur lors du retrait: " . $e->getMessage());
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
        try {

            if ($montant <= 0) {
                throw new \InvalidArgumentException("Le montant du transfert doit être positif");
            }
            
            $app = \App\Core\App::getInstance();
            $compteRepository = $app->getDependency('compteRepository');
            

            $compteSource = $compteRepository->findByTelephone($compteSourceTel);
            if (!$compteSource) {
                throw new \Exception("Compte source non trouvé");
            }
            
            $compteDest = $compteRepository->findByTelephone($compteDestTel);
            if (!$compteDest) {
                throw new \Exception("Compte destinataire non trouvé");
            }
            

            if ((float)$compteSource['solde'] < $montant) {
                throw new \Exception("Solde insuffisant pour effectuer le transfert");
            }
            

            $nouveauSoldeSource = (float)$compteSource['solde'] - $montant;
            $nouveauSoldeDest = (float)$compteDest['solde'] + $montant;
            
            $updateSoldeSource = $compteRepository->updateSolde($compteSourceTel, $nouveauSoldeSource);
            $updateSoldeDest = $compteRepository->updateSolde($compteDestTel, $nouveauSoldeDest);
            
            if (!$updateSoldeSource || !$updateSoldeDest) {
                error_log("Erreur lors de la mise à jour des soldes");
                return false;
            }
            

            return $this->createTransfertTransaction($compteSourceTel, $compteDestTel, $montant, $motif);
        } catch (\Exception $e) {
            error_log("Erreur lors du transfert: " . $e->getMessage());
            return false;
        }
    }
}

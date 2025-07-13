<?php

namespace App\Services;

use App\Repository\CompteRepository;
use App\Services\TransactionService;

class CompteService
{
    private CompteRepository $compteRepository;
    private ?TransactionService $transactionService = null;

    public function __construct(CompteRepository $compteRepository = null, TransactionService $transactionService = null)
    {
        $this->compteRepository = $compteRepository ?: new CompteRepository();
        $this->transactionService = $transactionService;
    }
    
    public function setTransactionService(TransactionService $transactionService): void
    {
        $this->transactionService = $transactionService;
    }

    public function getSolde(string $telephone): float
    {
        $comptes = $this->compteRepository->findByPersonne($telephone);
        if (empty($comptes)) {
            return 0.0;
        }
        
        $soldeTotal = 0.0;
        foreach ($comptes as $compte) {
            $soldeTotal += (float)$compte['solde'];
        }
        
        return $soldeTotal;
    }
    

    public function getComptesByPersonne(string $telephone): array
    {
        return $this->compteRepository->findByPersonne($telephone);
    }
    

    public function getComptePrincipal(string $telephone): ?array
    {
        $comptes = $this->compteRepository->findByPersonne($telephone);
        error_log("Recherche du compte principal parmi " . count($comptes) . " comptes");
        
        if (empty($comptes)) {
            error_log("Aucun compte trouvé pour la personne: " . $telephone);
            return null;
        }
        
        error_log("DEBUG - Tous les comptes trouvés pour " . $telephone . ": " . json_encode($comptes));
        
        foreach ($comptes as $compte) {
            error_log("Analyse du compte " . $compte['telephone'] . " - Clés disponibles: " . implode(", ", array_keys($compte)));
            
            foreach ($compte as $key => $value) {
                error_log("Compte " . $compte['telephone'] . " - $key: " . (is_null($value) ? "NULL" : $value));
            }
            
            if (
                (isset($compte['typecompte']) && strtolower($compte['typecompte']) === 'principal') ||
                (isset($compte['type_compte']) && strtolower($compte['type_compte']) === 'principal') ||
                (isset($compte['type']) && strtolower($compte['type']) === 'principal') ||
                (isset($compte['compte_type']) && strtolower($compte['compte_type']) === 'principal')
            ) {
                error_log("Compte principal trouvé : " . $compte['telephone']);
                return $compte;
            }
        }
        
        error_log("Aucun compte principal trouvé pour le téléphone: " . $telephone);
        return null;
    }
    

    public function getComptesSecondaires(string $telephone): array
    {
        $result = [];
        $comptes = $this->compteRepository->findByPersonne($telephone);
        error_log("Recherche des comptes secondaires parmi " . count($comptes) . " comptes");
        
        foreach ($comptes as $compte) {

            if (
                (isset($compte['typecompte']) && strtolower($compte['typecompte']) === 'secondaire') ||
                (isset($compte['type_compte']) && strtolower($compte['type_compte']) === 'secondaire') ||
                (isset($compte['type']) && strtolower($compte['type']) === 'secondaire') ||
                (isset($compte['compte_type']) && strtolower($compte['compte_type']) === 'secondaire')
            ) {
                error_log("Compte secondaire trouvé : " . $compte['telephone']);
                $result[] = $compte;
            }
        }
        
        error_log("Total de comptes secondaires trouvés : " . count($result));
        return $result;
    }
    
    public function createCompteSecondaire(string $personneTelephone, string $numCompteSecondaire, float $montantInitial = 0): bool
    {
        try {

            $comptePrincipal = $this->getComptePrincipal($personneTelephone);
            if (!$comptePrincipal) {
                error_log("Pas de compte principal pour la personne: $personneTelephone");
                return false;
            }
            

            if ($montantInitial > 0 && (float)$comptePrincipal['solde'] < $montantInitial) {
                error_log("Solde insuffisant dans le compte principal");
                return false;
            }
            

            $compteData = [
                'telephone' => $numCompteSecondaire,
                'solde' => $montantInitial,
                'personne_telephone' => $personneTelephone,
                'typecompte' => 'secondaire'
            ];
            
            $compteId = $this->compteRepository->create($compteData);
            
            if (!$compteId) {
                error_log("Échec de la création du compte secondaire");
                return false;
            }
            

            if ($montantInitial > 0 && $this->transactionService) {
                $comptePrincipalTel = $comptePrincipal['telephone'];
                

                $nouveauSoldePrincipal = (float)$comptePrincipal['solde'] - $montantInitial;
                $this->compteRepository->updateSolde($comptePrincipalTel, $nouveauSoldePrincipal);
                

                $transactionOk = $this->transactionService->createTransfertTransaction(
                    $comptePrincipalTel,
                    $numCompteSecondaire,
                    $montantInitial
                );
                
                if (!$transactionOk) {
                    error_log("Échec de la création des transactions de transfert");
                    return false;
                }
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la création du compte secondaire: " . $e->getMessage());
            return false;
        }
    }

    
    public function changeCompteToPrincipal(string $personneTelephone, string $compteSecondaireTelephone): bool
    {
        try {
            error_log("Tentative de changement du compte $compteSecondaireTelephone en compte principal pour $personneTelephone");
            
            $compteSecondaire = $this->compteRepository->findByTelephone($compteSecondaireTelephone);
            
            if (!$compteSecondaire || $compteSecondaire['personne_telephone'] !== $personneTelephone) {
                error_log("Le compte secondaire n'existe pas ou n'appartient pas à cette personne");
                return false;
            }
            
            if (!isset($compteSecondaire['typecompte']) || $compteSecondaire['typecompte'] !== 'secondaire') {
                error_log("Le compte n'est pas un compte secondaire");
                return false;
            }
            
            $comptePrincipal = $this->getComptePrincipal($personneTelephone);
            if (!$comptePrincipal) {
                error_log("Aucun compte principal trouvé pour la personne $personneTelephone");
                return false;
            }
            
            $this->compteRepository->beginTransaction();
            
            try {

                $successDemotion = $this->compteRepository->updateTypeCompte($comptePrincipal['telephone'], 'secondaire');
                if (!$successDemotion) {
                    throw new \Exception("Impossible de changer l'ancien compte principal en secondaire");
                }
                

                $successPromotion = $this->compteRepository->updateTypeCompte($compteSecondaireTelephone, 'principal');
                if (!$successPromotion) {
                    throw new \Exception("Impossible de changer le compte secondaire en principal");
                }
                

                $this->compteRepository->commit();
                error_log("Changement du compte principal réussi");
                return true;
            } catch (\Exception $e) {

                $this->compteRepository->rollBack();
                error_log("Erreur lors du changement de compte principal : " . $e->getMessage());
                return false;
            }
        } catch (\Exception $e) {
            error_log("Erreur lors du changement de compte principal : " . $e->getMessage());
            return false;
        }
    }
}
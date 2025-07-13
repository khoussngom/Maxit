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
}
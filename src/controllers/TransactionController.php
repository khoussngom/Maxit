<?php

namespace App\Controllers;

use App\Core\App;
use App\Abstract\AbstractController;

class TransactionController extends AbstractController
{

    
    public function index(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            

            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 7;
            $perPage = in_array($perPage, [7, 15, 25, 50]) ? $perPage : 7;
            
            $filters = $this->getTransactionFilters();
            

            $comptes = $this->getComptesUtilisateur($telephone);
            $result = $this->getTransactionsUtilisateur($telephone, $filters, $page, $perPage);
            
            $success = $this->session->getFlash('success');
            $error = $this->session->getFlash('error');
            
            $this->renderHtml('transactions', [
                'user' => $user,
                'comptes' => $comptes ?? [],
                'transactions' => $result['transactions'] ?? [],
                'pagination' => $result['pagination'] ?? [],
                'filters' => $filters,
                'success' => $success,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            $this->handleError($e, 'transactions');
        }
    }
    

    

    public function create(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            
            $app = App::getInstance();
            $compteRepository = $app->getDependency('compteRepository');
            $comptes = $compteRepository->findByPersonne($telephone);
            
            $error = $this->session->getFlash('error');
            
            $this->renderHtml('create-transaction', [
                'user' => $user,
                'comptes' => $comptes,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            $this->handleError($e, 'création de transaction');
        }
    }
    

    public function store(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            
            $transactionData = $this->validateTransactionData();
            
            $transactionService = App::getInstance()->getDependency('transactionService');
            
            error_log("Traitement de la transaction: " . json_encode($transactionData));
            $success = false;
            $error = null;
            
            try {

                switch ($transactionData['type']) {
                    case 'depot':
                        $success = $transactionService->effectuerDepot(
                            $transactionData['compteSource'], 
                            $transactionData['montant'], 
                            $transactionData['motif']
                        );
                        break;
                    case 'retrait':
                        $success = $transactionService->effectuerRetrait(
                            $transactionData['compteSource'], 
                            $transactionData['montant'], 
                            $transactionData['motif']
                        );
                        break;
                    case 'transfert':
                        $success = $transactionService->effectuerTransfert(
                            $transactionData['compteSource'], 
                            $transactionData['destination'], 
                            $transactionData['montant'], 
                            $transactionData['motif']
                        );
                        break;
                    default:
                        $error = 'Type de transaction non reconnu.';
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                error_log("Exception dans le traitement de la transaction: " . $e->getMessage());
            }
            
            if ($success) {
                error_log("Transaction réussie!");
                $this->session->setFlash('success', 'La transaction a été effectuée avec succès.');
                header('Location: /transactions');
                exit;
            } else {
                $message = $error ?? 'Une erreur est survenue lors de la transaction.';
                error_log("Échec de la transaction: " . $message);
                $this->session->setFlash('error', $message);
                header('Location: /transactions/create');
                exit;
            }
        } catch (\Exception $e) {
            $this->handleStoreError($e, 'transactions/create', 'la transaction');
        }
    }
    

    private function getTransactionFilters(): array
    {
        $filters = [
            'type' => $_GET['type'] ?? null,
            'date' => $_GET['date'] ?? null
        ];
        
        return array_filter($filters);
    }
    

    private function getComptesUtilisateur(string $telephone): array
    {
        try {
            $compteRepository = App::getInstance()->getDependency('compteRepository');
            return $compteRepository->findByPersonne($telephone);
        } catch (\Exception $e) {
            error_log('Erreur lors de la récupération des comptes: ' . $e->getMessage());
            return [];
        }
    }
    
    private function getTransactionsUtilisateur(string $telephone, array $filters, int $page, int $perPage): array
    {
        try {
            error_log("Récupération des transactions pour l'utilisateur {$telephone}, page {$page}");
            $app = App::getInstance();
            $transactionRepository = $app->getDependency('transactionRepository');
            
            if ($transactionRepository && method_exists($transactionRepository, 'findAllByPersonneWithFilters')) {
                $result = $transactionRepository->findAllByPersonneWithFilters($telephone, $filters, $page, $perPage);
                error_log('Transactions récupérées avec succès: ' . count($result['transactions']));
                
                if (empty($result['transactions'])) {

                    error_log('Vérification de l\'existence de transactions sans filtres');
                    $checkResult = $transactionRepository->findAllByPersonneWithFilters($telephone, [], 1, 1);
                    if (!empty($checkResult['transactions'])) {
                        error_log('Des transactions existent sans filtres, le problème vient probablement des filtres appliqués');
                    } else {
                        error_log('Aucune transaction trouvée pour cet utilisateur, même sans filtres');
                    }
                }
                
                return $result;
            } else {
                error_log('Le repository de transaction ou la méthode findAllByPersonneWithFilters n\'existe pas');
            }
        } catch (\Exception $e) {
            error_log('Erreur lors de la récupération des transactions: ' . $e->getMessage());
            error_log('Trace: ' . $e->getTraceAsString());
        }
        
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
    

    private function validateTransactionData(): array
    {
        $type = $_POST['type'] ?? null;
        $compteSource = $_POST['compte_telephone'] ?? null;
        $destination = $_POST['destination_telephone'] ?? null;
        $montant = isset($_POST['montant']) ? (float) $_POST['montant'] : 0;
        $motif = $_POST['motif'] ?? '';
        
        if (empty($type)) {
            $this->session->setFlash('error', 'Le type de transaction est obligatoire.');
            header('Location: /transactions/create');
            exit;
        }
        
        if (empty($compteSource)) {
            $this->session->setFlash('error', 'Le compte source est obligatoire.');
            header('Location: /transactions/create');
            exit;
        }
        
        if ($type === 'transfert' && empty($destination)) {
            $this->session->setFlash('error', 'Le compte destinataire est obligatoire pour un transfert.');
            header('Location: /transactions/create');
            exit;
        }
        
        if ($montant <= 0) {
            $this->session->setFlash('error', 'Le montant doit être supérieur à zéro.');
            header('Location: /transactions/create');
            exit;
        }
        
        return [
            'type' => $type,
            'compteSource' => $compteSource,
            'destination' => $destination,
            'montant' => $montant,
            'motif' => $motif
        ];
    }
    

    private function checkAuthentication()
    {
        $user = $this->session->get('user');
        
        if (!$user) {
            header('Location: /login');
            exit;
        }
        
        return $user;
    }
    

    private function getUserTelephone($user)
    {
        $telephone = $this->session->get('user_id');
        
        if (!$telephone && $user && method_exists($user, 'getTelephone')) {
            $telephone = $user->getTelephone();
            if ($telephone) {
                $this->session->set('user_id', $telephone);
            }
        }
        
        if (empty($telephone)) {
            error_log('Erreur: Téléphone utilisateur non trouvé dans la session ou l\'objet utilisateur');
            header('Location: /login');
            exit;
        }
        
        return (string) $telephone;
    }
    

    private function handleError(\Exception $e, $pageType)
    {
        error_log("Erreur dans TransactionController pour $pageType: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        
        $this->renderHtml('error', [
            'message' => "Une erreur est survenue lors du chargement de la page de $pageType."
        ]);
    }
    
    private function handleStoreError(\Exception $e, $redirectUrl, $operationType)
    {
        error_log("Erreur dans TransactionController lors de $operationType: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        
        $this->session->setFlash('error', "Une erreur est survenue lors de $operationType: " . $e->getMessage());
        
        header("Location: /$redirectUrl");
        exit;
    }
    
    public function update(): void {}
    public function show(): void {}
    public function edit(): void {}
    public function destroy() { return null; }
}

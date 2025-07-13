<?php

namespace App\Controllers;

use App\Core\App;
use App\Abstract\AbstractController;

class AcceuilController extends AbstractController
{
    public function index():void
    {
        try {
            $app = App::getInstance();
            $session = $app->getDependency('session');
            
            $user = $session->get('user');
            
            if (!$user) {
                header('Location: /login');
                exit;
            }
            

            $telephone = $session->get('user_id');
            

            if (!$telephone && $user && method_exists($user, 'getTelephone')) {
                $telephone = $user->getTelephone();
                

                error_log('Téléphone récupéré de l\'objet user: ' . ($telephone ?: 'non disponible'));
                

                if ($telephone) {
                    $session->set('user_id', $telephone);
                }
            }
            

            if (empty($telephone)) {
                error_log('Erreur: Téléphone utilisateur non trouvé dans la session ou l\'objet utilisateur');
                header('Location: /login');
                exit;
            }
            

            $telephone = (string) $telephone;
            
            $compteRepository = $app->getDependency('compteRepository');
            $transactionRepository = $app->getDependency('transactionRepository');
            
            error_log('Type de transactionRepository: ' . gettype($transactionRepository));
            if (is_object($transactionRepository)) {
                error_log('Classe de transactionRepository: ' . get_class($transactionRepository));
            }
            
            error_log('Téléphone utilisateur utilisé: ' . $telephone);
            
            $comptes = [];
            try {
                $comptes = $compteRepository->findByPersonne($telephone);
                error_log('Nombre de comptes trouvés: ' . count($comptes));
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des comptes: ' . $e->getMessage());
                error_log($e->getTraceAsString());

            }
            
            $transactions = [];
            try {
                if ($transactionRepository && method_exists($transactionRepository, 'findRecentByPersonne')) {
                    $transactions = $transactionRepository->findRecentByPersonne($telephone, 10);
                    error_log('Nombre de transactions trouvées: ' . count($transactions));
                } else {
                    error_log('TransactionRepository ou méthode findRecentByPersonne non disponible');
                }
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des transactions: ' . $e->getMessage());
                error_log($e->getTraceAsString());

            }
            

            $success = $session->getFlash('success');
            $error = $session->getFlash('error');
            
            $this->renderHtml('accueil', [
                'user' => $user,
                'comptes' => $comptes ?? [],
                'transactions' => $transactions,
                'success' => $success,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            error_log("Erreur dans AcceuilController::index : " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            $this->renderHtml('error', [
                'message' => 'Une erreur est survenue lors du chargement de la page d\'accueil.'
            ]);
        }
    }
    
    public function create(): void {}
    public function store(): void {}
    public function update(): void {}
    public function show(): void {}
    public function edit(): void {}
    public function destroy() { return null; }
    
    public function transactions(): void
    {
        try {
            $app = App::getInstance();
            $session = $app->getDependency('session');
            
            $user = $session->get('user');
            
            if (!$user) {
                header('Location: /login');
                exit;
            }
            

            $telephone = $session->get('user_id');
            
            if (!$telephone && $user && method_exists($user, 'getTelephone')) {
                $telephone = $user->getTelephone();
                if ($telephone) {
                    $session->set('user_id', $telephone);
                }
            }
            

            if (empty($telephone)) {
                error_log('Erreur: Téléphone utilisateur non trouvé dans la session ou l\'objet utilisateur');
                header('Location: /login');
                exit;
            }
            

            $telephone = (string) $telephone;
            

            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 7;
            

            $perPage = in_array($perPage, [7, 15, 25, 50]) ? $perPage : 7;
            

            $filters = [
                'type' => $_GET['type'] ?? null,
                'date' => $_GET['date'] ?? null
            ];
            

            $filters = array_filter($filters);
            
            $transactionRepository = $app->getDependency('transactionRepository');
            $compteRepository = $app->getDependency('compteRepository');
            

            $comptes = [];
            try {
                $comptes = $compteRepository->findByPersonne($telephone);
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des comptes: ' . $e->getMessage());
            }
            

            $result = [];
            try {
                if ($transactionRepository && method_exists($transactionRepository, 'findAllByPersonneWithFilters')) {
                    $result = $transactionRepository->findAllByPersonneWithFilters($telephone, $filters, $page, $perPage);
                    error_log('Transactions récupérées avec succès: ' . count($result['transactions']));
                } else {
                    error_log('TransactionRepository ou méthode findAllByPersonneWithFilters non disponible');
                }
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des transactions: ' . $e->getMessage());
                error_log($e->getTraceAsString());
            }
            

            if (empty($result)) {
                $result = [
                    'transactions' => [],
                    'pagination' => [
                        'total' => 0,
                        'perPage' => $perPage,
                        'currentPage' => $page,
                        'totalPages' => 0
                    ]
                ];
            }
            

            $success = $session->getFlash('success');
            $error = $session->getFlash('error');
            

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
            error_log("Erreur dans AcceuilController::transactions : " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            $this->renderHtml('error', [
                'message' => 'Une erreur est survenue lors du chargement de la page des transactions.'
            ]);
        }
    }
    
    public function comptes(): void
    {
        try {
            $app = App::getInstance();
            $session = $app->getDependency('session');
            
            $user = $session->get('user');
            
            if (!$user) {
                header('Location: /login');
                exit;
            }
            

            $telephone = $session->get('user_id');
            
            if (!$telephone && $user && method_exists($user, 'getTelephone')) {
                $telephone = $user->getTelephone();
                if ($telephone) {
                    $session->set('user_id', $telephone);
                }
            }
            

            if (empty($telephone)) {
                error_log('Erreur: Téléphone utilisateur non trouvé dans la session ou l\'objet utilisateur');
                header('Location: /login');
                exit;
            }
            
            $telephone = (string) $telephone;
            

            $compteService = $app->getDependency('compteService');
            
            $comptePrincipal = null;
            $comptesSecondaires = [];
            
            try {
                if (method_exists($compteService, 'getComptePrincipal')) {
                    $comptePrincipal = $compteService->getComptePrincipal($telephone);
                }
                
                if (method_exists($compteService, 'getComptesSecondaires')) {
                    $comptesSecondaires = $compteService->getComptesSecondaires($telephone);
                }
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des comptes: ' . $e->getMessage());
                error_log($e->getTraceAsString());
            }
            

            $success = $session->getFlash('success');
            $error = $session->getFlash('error');
            

            $this->renderHtml('comptes', [
                'user' => $user,
                'comptePrincipal' => $comptePrincipal,
                'comptesSecondaires' => $comptesSecondaires,
                'success' => $success,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            error_log("Erreur dans AcceuilController::comptes : " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            $this->renderHtml('error', [
                'message' => 'Une erreur est survenue lors du chargement de la page des comptes.'
            ]);
        }
    }
    
    public function createCompteSecondaire(): void
    {
        try {
            $app = App::getInstance();
            $session = $app->getDependency('session');
            
            $user = $session->get('user');
            
            if (!$user) {
                header('Location: /login');
                exit;
            }
            
            $telephone = $session->get('user_id');
            if (!$telephone) {
                header('Location: /login');
                exit;
            }
            

            $compteService = $app->getDependency('compteService');
            $comptePrincipal = $compteService->getComptePrincipal($telephone);
            
            if (!$comptePrincipal) {
                $session->setFlash('error', 'Vous devez avoir un compte principal avant de créer un compte secondaire.');
                header('Location: /comptes');
                exit;
            }
            

            $this->renderHtml('create-compte-secondaire', [
                'user' => $user,
                'comptePrincipal' => $comptePrincipal
            ]);
        } catch (\Exception $e) {
            error_log("Erreur dans AcceuilController::createCompteSecondaire : " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            $this->renderHtml('error', [
                'message' => 'Une erreur est survenue lors du chargement de la page de création de compte secondaire.'
            ]);
        }
    }
    
    public function storeCompteSecondaire(): void
    {
        try {
            $app = App::getInstance();
            $session = $app->getDependency('session');
            
            $user = $session->get('user');
            
            if (!$user) {
                header('Location: /login');
                exit;
            }
            
            $telephone = $session->get('user_id');
            if (!$telephone) {
                header('Location: /login');
                exit;
            }
            

            $numCompteSecondaire = $_POST['telephone'] ?? null;
            $montantInitial = isset($_POST['montant_initial']) ? (float) $_POST['montant_initial'] : 0;
            

            if (empty($numCompteSecondaire)) {
                $session->setFlash('error', 'Le numéro de téléphone du compte secondaire est obligatoire.');
                header('Location: /comptes/secondaire/create');
                exit;
            }
            
            if ($montantInitial < 0) {
                $session->setFlash('error', 'Le montant initial ne peut pas être négatif.');
                header('Location: /comptes/secondaire/create');
                exit;
            }
            

            $compteService = $app->getDependency('compteService');
            

            $success = $compteService->createCompteSecondaire($telephone, $numCompteSecondaire, $montantInitial);
            
            if ($success) {
                $session->setFlash('success', 'Le compte secondaire a été créé avec succès.');
                header('Location: /comptes');
                exit;
            } else {
                $session->setFlash('error', 'Une erreur est survenue lors de la création du compte secondaire.');
                header('Location: /comptes/secondaire/create');
                exit;
            }
        } catch (\Exception $e) {
            error_log("Erreur dans AcceuilController::storeCompteSecondaire : " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            $app = App::getInstance();
            $session = $app->getDependency('session');
            $session->setFlash('error', 'Une erreur est survenue lors de la création du compte secondaire.');
            
            header('Location: /comptes/secondaire/create');
            exit;
        }
    }
    
    public function createTransaction(): void
    {
        try {
            $app = App::getInstance();
            $session = $app->getDependency('session');
            
            $user = $session->get('user');
            
            if (!$user) {
                header('Location: /login');
                exit;
            }
            

            $telephone = $session->get('user_id');
            
            if (!$telephone && $user && method_exists($user, 'getTelephone')) {
                $telephone = $user->getTelephone();
                if ($telephone) {
                    $session->set('user_id', $telephone);
                }
            }
            

            if (empty($telephone)) {
                error_log('Erreur: Téléphone utilisateur non trouvé dans la session ou l\'objet utilisateur');
                header('Location: /login');
                exit;
            }
            
            $telephone = (string) $telephone;
            

            $compteService = $app->getDependency('compteService');
            $comptes = [];
            
            try {

                $compteRepository = $app->getDependency('compteRepository');
                $comptes = $compteRepository->findByPersonne($telephone);
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des comptes: ' . $e->getMessage());
                error_log($e->getTraceAsString());
            }
            

            $error = $session->getFlash('error');
            

            $this->renderHtml('create-transaction', [
                'user' => $user,
                'comptes' => $comptes,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            error_log("Erreur dans AcceuilController::createTransaction : " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            $this->renderHtml('error', [
                'message' => 'Une erreur est survenue lors du chargement de la page de création de transaction.'
            ]);
        }
    }
    
    public function storeTransaction(): void
    {
        try {
            $app = App::getInstance();
            $session = $app->getDependency('session');
            
            $user = $session->get('user');
            
            if (!$user) {
                header('Location: /login');
                exit;
            }
            
            $telephone = $session->get('user_id');
            if (!$telephone) {
                header('Location: /login');
                exit;
            }
            

            $type = $_POST['type'] ?? null;
            $compteSource = $_POST['compte_telephone'] ?? null;
            $destination = $_POST['destination_telephone'] ?? null;
            $montant = isset($_POST['montant']) ? (float) $_POST['montant'] : 0;
            $motif = $_POST['motif'] ?? '';
            

            if (empty($type)) {
                $session->setFlash('error', 'Le type de transaction est obligatoire.');
                header('Location: /transactions/create');
                exit;
            }
            
            if (empty($compteSource)) {
                $session->setFlash('error', 'Le compte source est obligatoire.');
                header('Location: /transactions/create');
                exit;
            }
            
            if ($type === 'transfert' && empty($destination)) {
                $session->setFlash('error', 'Le compte destinataire est obligatoire pour un transfert.');
                header('Location: /transactions/create');
                exit;
            }
            
            if ($montant <= 0) {
                $session->setFlash('error', 'Le montant doit être supérieur à zéro.');
                header('Location: /transactions/create');
                exit;
            }
            

            $transactionService = $app->getDependency('transactionService');
            

            $success = false;
            
            switch ($type) {
                case 'depot':
                    $success = $transactionService->effectuerDepot($compteSource, $montant, $motif);
                    break;
                case 'retrait':
                    $success = $transactionService->effectuerRetrait($compteSource, $montant, $motif);
                    break;
                case 'transfert':
                    $success = $transactionService->effectuerTransfert($compteSource, $destination, $montant, $motif);
                    break;
                default:
                    $session->setFlash('error', 'Type de transaction non reconnu.');
                    header('Location: /transactions/create');
                    exit;
            }
            
            if ($success) {
                $session->setFlash('success', 'La transaction a été effectuée avec succès.');
                header('Location: /transactions');
                exit;
            } else {
                $session->setFlash('error', 'Une erreur est survenue lors de la transaction.');
                header('Location: /transactions/create');
                exit;
            }
        } catch (\Exception $e) {
            error_log("Erreur dans AcceuilController::storeTransaction : " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            $app = App::getInstance();
            $session = $app->getDependency('session');
            $session->setFlash('error', 'Une erreur est survenue lors de la transaction: ' . $e->getMessage());
            
            header('Location: /transactions/create');
            exit;
        }
    }
}
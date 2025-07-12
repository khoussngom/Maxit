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
            
            // Récupération du téléphone de l'utilisateur (qui est notre clé primaire)
            $telephone = $session->get('user_id');
            
            // Si user_id n'existe pas dans la session, on essaie de le récupérer depuis l'objet user
            if (!$telephone && $user && method_exists($user, 'getTelephone')) {
                $telephone = $user->getTelephone();
                
                // Log pour le débogage
                error_log('Téléphone récupéré de l\'objet user: ' . ($telephone ?: 'non disponible'));
                
                // Stocke le téléphone dans la session pour les prochaines utilisations
                if ($telephone) {
                    $session->set('user_id', $telephone);
                }
            }
            
            // Vérification que le téléphone est bien défini
            if (empty($telephone)) {
                error_log('Erreur: Téléphone utilisateur non trouvé dans la session ou l\'objet utilisateur');
                header('Location: /login');
                exit;
            }
            
            // S'assurer que le téléphone est bien une chaîne de caractères
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
                // Continuer l'exécution avec un tableau vide
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
                // Continuer l'exécution avec un tableau vide
            }
            
            $this->renderHtml('accueil', [
                'user' => $user,
                'comptes' => $comptes ?? [],
                'transactions' => $transactions
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
            
            // Récupération du téléphone de l'utilisateur
            $telephone = $session->get('user_id');
            
            if (!$telephone && $user && method_exists($user, 'getTelephone')) {
                $telephone = $user->getTelephone();
                if ($telephone) {
                    $session->set('user_id', $telephone);
                }
            }
            
            // Vérification que le téléphone est bien défini
            if (empty($telephone)) {
                error_log('Erreur: Téléphone utilisateur non trouvé dans la session ou l\'objet utilisateur');
                header('Location: /login');
                exit;
            }
            
            // S'assurer que le téléphone est bien une chaîne de caractères
            $telephone = (string) $telephone;
            
            // Récupération des paramètres de filtrage et pagination
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 7;
            
            // Validation du nombre d'éléments par page
            $perPage = in_array($perPage, [7, 15, 25, 50]) ? $perPage : 7;
            
            // Filtres
            $filters = [
                'type' => $_GET['type'] ?? null,
                'date' => $_GET['date'] ?? null
            ];
            
            // Nettoyage des filtres vides
            $filters = array_filter($filters);
            
            $transactionRepository = $app->getDependency('transactionRepository');
            $compteRepository = $app->getDependency('compteRepository');
            
            // Récupération des comptes pour afficher le solde
            $comptes = [];
            try {
                $comptes = $compteRepository->findByPersonne($telephone);
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des comptes: ' . $e->getMessage());
            }
            
            // Récupération des transactions avec filtrage et pagination
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
            
            // Si result est vide, initialiser avec une structure par défaut
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
            
            // Rendre la vue
            $this->renderHtml('transactions', [
                'user' => $user,
                'comptes' => $comptes ?? [],
                'transactions' => $result['transactions'] ?? [],
                'pagination' => $result['pagination'] ?? [],
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            error_log("Erreur dans AcceuilController::transactions : " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            $this->renderHtml('error', [
                'message' => 'Une erreur est survenue lors du chargement de la page des transactions.'
            ]);
        }
    }
}
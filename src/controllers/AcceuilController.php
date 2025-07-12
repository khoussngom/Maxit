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
            
            $compteRepository = $app->getDependency('compteRepository');
            $transactionRepository = $app->getDependency('transactionRepository');
            
            error_log('Type de transactionRepository: ' . gettype($transactionRepository));
            if (is_object($transactionRepository)) {
                error_log('Classe de transactionRepository: ' . get_class($transactionRepository));
            }
            
            $telephone = $session->get('user_id');
            $comptes = $compteRepository->findByPersonne($telephone);
            
            error_log('Nombre de comptes trouvés: ' . count($comptes));
            
            $transactions = [];
            try {
                $transactions = $transactionRepository->findRecentByPersonne($telephone, 10);
                error_log('Nombre de transactions trouvées: ' . count($transactions));
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des transactions: ' . $e->getMessage());
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
}
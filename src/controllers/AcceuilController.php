<?php

namespace App\Controllers;

use App\Core\App;
use App\Abstract\AbstractController;

class AcceuilController extends AbstractController
{

    public function index(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            
            $comptes = $this->getComptesUtilisateur($telephone);
            $transactions = $this->getTransactionsRecentes($telephone);
            
            $success = $this->session->getFlash('success');
            $error = $this->session->getFlash('error');
            
            $this->renderHtml('accueil', [
                'user' => $user,
                'comptes' => $comptes ?? [],
                'transactions' => $transactions,
                'success' => $success,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            $this->handleError($e, 'accueil');
        }
    }
    

    private function getComptesUtilisateur(string $telephone): array
    {
        try {
            $compteRepository = App::getInstance()->getDependency('compteRepository');
            $comptes = $compteRepository->findByPersonne($telephone);
            return $comptes;
        } catch (\Exception $e) {
            error_log('Erreur lors de la récupération des comptes: ' . $e->getMessage());
            return [];
        }
    }

    private function getTransactionsRecentes(string $telephone): array
    {
        try {
            $transactionRepository = App::getInstance()->getDependency('transactionRepository');
            
            if ($transactionRepository && method_exists($transactionRepository, 'findRecentByPersonne')) {
                return $transactionRepository->findRecentByPersonne($telephone, 10);
            }
        } catch (\Exception $e) {
            error_log('Erreur lors de la récupération des transactions: ' . $e->getMessage());
        }
        
        return [];
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
        error_log("Erreur dans AcceuilController pour $pageType: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        
        $this->renderHtml('error', [
            'message' => "Une erreur est survenue lors du chargement de la page de $pageType."
        ]);
    }
    
    public function create(): void {}
    public function store(): void {}
    public function update(): void {}
    public function show(): void {}
    public function edit(): void {}
    public function destroy() { return null; }
}
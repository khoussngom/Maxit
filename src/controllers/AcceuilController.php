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
            
            $telephone = $session->get('user_id');
            $comptes = $compteRepository->findByPersonne($telephone);
            
            $this->renderHtml('accueil', [
                'user' => $user,
                'comptes' => $comptes ?? []
            ]);
        } catch (\Exception $e) {
            error_log("Erreur dans AcceuilController::index : " . $e->getMessage());
            
            $this->renderHtml('error', [
                'message' => 'Une erreur est survenue lors du chargement de la page d\'accueil.'
            ]);
        }
    }
    
    public function create(): void
    {
    }
    
    public function store(): void
    {
    }
    
    public function update(): void
    {
    }
    
    public function show(): void
    {
    }
    
    public function edit(): void
    {
    }
    
    public function destroy()
    {
        return null;
    }
}
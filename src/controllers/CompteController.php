<?php

namespace App\Controllers;

use App\Core\App;
use App\Abstract\AbstractController;

class CompteController extends AbstractController
{
    public function index(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            
            $compteService = App::getInstance()->getDependency('compteService');
            
            $comptePrincipal = null;
            $comptesSecondaires = [];
            
            try {
                $comptePrincipal = $compteService->getComptePrincipal($telephone);
                $comptesSecondaires = $compteService->getComptesSecondaires($telephone);
            } catch (\Exception $e) {
                error_log('Erreur lors de la récupération des comptes: ' . $e->getMessage());
            }
            
            $success = $this->session->getFlash('success');
            $error = $this->session->getFlash('error');
            
            $this->renderHtml('comptes', [
                'user' => $user,
                'comptePrincipal' => $comptePrincipal,
                'comptesSecondaires' => $comptesSecondaires,
                'success' => $success,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            $this->handleError($e, 'comptes');
        }
    }

    public function create(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            
            $compteService = App::getInstance()->getDependency('compteService');
            $comptePrincipal = $compteService->getComptePrincipal($telephone);
            
            if (!$comptePrincipal) {
                $this->session->setFlash('error', 'Vous devez avoir un compte principal avant de créer un compte secondaire.');
                header('Location: /comptes');
                exit;
            }
            
            $this->renderHtml('create-compte-secondaire', [
                'user' => $user,
                'comptePrincipal' => $comptePrincipal
            ]);
        } catch (\Exception $e) {
            $this->handleError($e, 'création de compte secondaire');
        }
    }

    public function store(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            
            $numCompteSecondaire = $this->validateSecondaryAccount();
            $montantInitial = isset($_POST['montant_initial']) ? (float) $_POST['montant_initial'] : 0;
            
            $compteService = App::getInstance()->getDependency('compteService');
            $success = $compteService->createCompteSecondaire($telephone, $numCompteSecondaire, $montantInitial);
            
            if ($success) {
                $this->session->setFlash('success', 'Le compte secondaire a été créé avec succès.');
                header('Location: /comptes');
            } else {
                $this->session->setFlash('error', 'Une erreur est survenue lors de la création du compte secondaire.');
                header('Location: /comptes/secondaire/create');
            }
            exit;
        } catch (\Exception $e) {
            $this->handleStoreError($e, 'comptes/secondaire/create', 'création du compte secondaire');
        }
    }
    
    public function changePrincipal(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            
            $compteService = App::getInstance()->getDependency('compteService');
            $comptePrincipal = $compteService->getComptePrincipal($telephone);
            $comptesSecondaires = $compteService->getComptesSecondaires($telephone);
            
            $compteSelectionne = $this->getSelectedAccount($comptesSecondaires);
            
            $success = $this->session->getFlash('success');
            $error = $this->session->getFlash('error');
            
            $this->renderHtml('change-compte-principal', [
                'user' => $user,
                'comptePrincipal' => $comptePrincipal,
                'comptesSecondaires' => $comptesSecondaires,
                'compteSelectionne' => $compteSelectionne,
                'success' => $success,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            $this->handleError($e, 'changement de compte principal');
        }
    }

    public function storeChangePrincipal(): void
    {
        try {
            $user = $this->checkAuthentication();
            $telephone = $this->getUserTelephone($user);
            
            if (empty($_POST['compte_id'])) {
                $this->session->setFlash('error', 'Paramètres invalides pour le changement de compte principal.');
                header('Location: /comptes');
                exit;
            }
            
            $compteSecondaireId = $_POST['compte_id'];
            
            $compteService = App::getInstance()->getDependency('compteService');
            $result = $compteService->changeCompteToPrincipal($telephone, $compteSecondaireId);
            
            if ($result) {
                $this->session->setFlash('success', 'Le compte a été défini comme compte principal avec succès.');
            } else {
                $this->session->setFlash('error', 'Une erreur est survenue lors du changement de compte principal.');
            }
            
            header('Location: /comptes');
            exit;
        } catch (\Exception $e) {
            $this->handleStoreError($e, 'comptes', 'changement de compte principal');
        }
    }

    public function recherche(): void
    {
        try {
            $user = $this->checkAuthentication();
            
            if (!isset($user->typepersonne) || $user->typepersonne !== 'commercial') {
                $this->session->setFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette fonctionnalité');
                header('Location: /accueil');
                exit;
            }
            
            $telephone = $_GET['telephone'] ?? null;
            $result = null;
            $personne = null;
            $transactions = [];
            $error = null;
            
            if ($telephone) {
                $app = App::getInstance();
                $compteRepository = $app->getDependency('compteRepository');
                $personneRepository = $app->getDependency('personneRepository');
                $transactionRepository = $app->getDependency('transactionRepository');
                
                try {
                    $compte = $compteRepository->findByTelephone($telephone);
                    
                    if (!$compte) {
                        $error = 'Aucun compte trouvé avec ce numéro';
                    } else {

                        if (isset($compte['personne_telephone'])) {
                            $personne = $personneRepository->findByTelephone($compte['personne_telephone']);
                        }
                        


                        $transactions = $transactionRepository->findRecentByCompte($telephone, 10);
                    }
                } catch (\Exception $e) {
                    error_log('Erreur lors de la recherche du compte: ' . $e->getMessage());
                    $error = 'Une erreur est survenue lors de la recherche';
                }
            }
            

            $this->renderHtml('recherche/compte', [
                'title' => 'Recherche de compte',
                'user' => $user,
                'telephone' => $telephone,
                'compte' => $compte ?? null,
                'personne' => $personne,
                'transactions' => $transactions,
                'error' => $error ?? $this->session->getFlash('error')
            ]);
        } catch (\Exception $e) {
            $this->handleError($e, 'recherche de compte');
        }
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

    private function validateSecondaryAccount()
    {
        $numCompteSecondaire = $_POST['telephone'] ?? null;
        
        if (empty($numCompteSecondaire)) {
            $this->session->setFlash('error', 'Le numéro de téléphone du compte secondaire est obligatoire.');
            header('Location: /comptes/secondaire/create');
            exit;
        }
        
        $montantInitial = isset($_POST['montant_initial']) ? (float) $_POST['montant_initial'] : 0;
        
        if ($montantInitial < 0) {
            $this->session->setFlash('error', 'Le montant initial ne peut pas être négatif.');
            header('Location: /comptes/secondaire/create');
            exit;
        }
        
        return $numCompteSecondaire;
    }

    private function getSelectedAccount($comptesSecondaires)
    {
        $compteId = isset($_GET['id']) ? $_GET['id'] : null;
        $compteSelectionne = null;
        
        if ($compteId) {
            foreach ($comptesSecondaires as $compte) {
                if ($compte['telephone'] === $compteId) {
                    $compteSelectionne = $compte;
                    break;
                }
            }
        }
        
        if (!$compteSelectionne && !empty($comptesSecondaires)) {
            $compteSelectionne = $comptesSecondaires[0];
        }
        
        return $compteSelectionne;
    }

    private function handleError(\Exception $e, $pageType)
    {
        error_log("Erreur dans CompteController pour $pageType: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        
        $this->renderHtml('error', [
            'message' => "Une erreur est survenue lors du chargement de la page de $pageType."
        ]);
    }

    private function handleStoreError(\Exception $e, $redirectUrl, $operationType)
    {
        error_log("Erreur dans CompteController lors de $operationType: " . $e->getMessage());
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

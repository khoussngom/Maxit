<?php

namespace App\Controllers;

use App\Core\App;
use App\Abstract\AbstractController;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;

class RechercheController extends AbstractController
{
    private CompteRepository $compteRepository;
    private TransactionRepository $transactionRepository;

    public function __construct()
    {
        parent::__construct();
        $app = App::getInstance();
        $this->compteRepository = $app->getDependency('compteRepository');
        $this->transactionRepository = $app->getDependency('transactionRepository');
    }

    public function rechercheCompte()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->renderHtml('recherche/compte', [
                'compte' => null,
                'transactions' => null,
                'errors' => $this->session->get('flash_errors') ?? [],
                'success' => $this->session->get('flash_success') ?? null
            ]);
            $this->session->set('flash_errors', null);
            $this->session->set('flash_success', null);
            return;
        }


        $telephone = trim($_POST['telephone'] ?? '');
        
        if (empty($telephone)) {
            $this->session->set('flash_errors', ['Le numéro de compte est obligatoire']);
            header('Location: ' . getenv('BASE_URL') . '/recherche/compte');
            exit;
        }


        $compte = $this->compteRepository->findByTelephone($telephone);
        
        if (!$compte) {
            $this->session->set('flash_errors', ['Aucun compte trouvé avec ce numéro']);
            header('Location: ' . getenv('BASE_URL') . '/recherche/compte');
            exit;
        }


        $transactions = $this->transactionRepository->findByCompte($telephone, 10);
        

        $this->renderHtml('recherche/compte', [
            'compte' => $compte,
            'transactions' => $transactions,
            'telephone' => $telephone
        ]);
    }

    public function listeTransactions($compteTelephone = null)
    {
        if ($compteTelephone === null) {
            $compteTelephone = $_GET['compte'] ?? null;
            
            if (!$compteTelephone) {
                $this->session->set('flash_errors', ['Numéro de compte manquant']);
                header('Location: ' . getenv('BASE_URL') . '/recherche/compte');
                exit;
            }
        }
        

        $compte = $this->compteRepository->findByTelephone($compteTelephone);
        if (!$compte) {
            $this->session->set('flash_errors', ['Compte non trouvé']);
            header('Location: ' . getenv('BASE_URL') . '/recherche/compte');
            exit;
        }


        $dateDebut = $_GET['dateDebut'] ?? null;
        $dateFin = $_GET['dateFin'] ?? null;
        $type = $_GET['type'] ?? null;
        

        $transactions = $this->transactionRepository->findByCompteWithFilters(
            $compteTelephone,
            $dateDebut,
            $dateFin,
            $type
        );
        
        $this->renderHtml('recherche/transactions', [
            'compte' => $compte,
            'transactions' => $transactions,
            'filtres' => [
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'type' => $type
            ]
        ]);
    }
}

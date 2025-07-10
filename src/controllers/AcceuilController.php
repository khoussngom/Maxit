<?php

namespace App\Controllers;

use App\Entity\PersonneEntity;
use App\Services\CompteService;
use App\Abstract\AbstractController;

class AcceuilController extends AbstractController
{
    private CompteService $compteService;

    public function __construct()
    {
        parent::__construct();
        $this->compteService = new CompteService();
    }

    public function index(): void
    {
    
        $user = $_SESSION['user'];
        $solde = $this->compteService->getSolde($user->getTelephone());
        
        $this->renderHtml('acceuil', [
            'user' => $user,
            'solde' => $solde
        ]);
    }
    public function create():void {}

    public function store():void {}

    public function show():void {}

    public function update():void {}

    public function edit():void {}
    public function destroy():void {}



}
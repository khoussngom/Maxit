<?php
use App\Controllers\AcceuilController;
use App\Controllers\SecurityController;

return $routes = [
    '/' => [
        'controller' => SecurityController::class,
        'action' => 'index'
    ],
    '/login' => [
        'controller' => SecurityController::class,
        'action' => 'login'
    ],
    '/logout' => [
        'controller' => SecurityController::class,
        'action' => 'logout'
    ],
    '/accueil' => [
        'controller' => AcceuilController::class,
        'middlewares' => ['auth'],
        'action' => 'index'
    ],
    '/commandes/create' => [
        'controller' => "CommandeController::class",
        'middlewares' => ['auth', 'isVendeur'],
        'action' => 'create'
    ], 
    '/commandes/store' => [
        'controller' => "CommandeController::class",
        'middlewares' => ['auth', 'isVendeur'],
        'action' => 'store'
    ],
    '/factures/show' => [
        'controller' => "FactureController::class",
        'middlewares' => ['auth', 'isVendeur'],
        'action' => 'show'  
    ], 
    '/client/commandes' => [
        'controller' => "CommandeController::class",
        'middlewares' => ['auth', 'isClient'],
        'action' => 'listComClient'
    ],
    '/inscription' => [
        'controller' => SecurityController::class,
        'action' => 'inscription'
    ],
    // '/accueil' => [
    //     'controller' => AcceuilController::class,
    //     'action' => 'index'
    // ]
];
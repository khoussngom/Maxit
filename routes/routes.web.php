<?php
use App\Controllers\AcceuilController;
use App\Controllers\SecurityController;

return $routes = [
    '/' => [
        'controller' => SecurityController::class,
        'action' => 'index',
        'method' => 'GET'
    ],
    'GET:/login' => [
        'controller' => SecurityController::class,
        'action' => 'index',
        'method' => 'GET'
    ],
    'POST:/login' => [
        'controller' => SecurityController::class,
        'action' => 'login',
        'method' => 'POST'
    ],
    '/logout' => [
        'controller' => SecurityController::class,
        'action' => 'logout',
        'method' => 'GET'
    ],
    '/accueil' => [
        'controller' => AcceuilController::class,
        'middlewares' => ['auth'],
        'action' => 'index',
        'method' => 'GET'
    ],
    '/transactions' => [
        'controller' => AcceuilController::class,
        'middlewares' => ['auth'],
        'action' => 'transactions',
        'method' => 'GET'
    ],
    '/comptes' => [
        'controller' => AcceuilController::class,
        'middlewares' => ['auth'],
        'action' => 'comptes',
        'method' => 'GET'
    ],
    'GET:/comptes/secondaire/create' => [
        'controller' => AcceuilController::class,
        'middlewares' => ['auth'],
        'action' => 'createCompteSecondaire',
        'method' => 'GET'
    ],
    'POST:/comptes/secondaire/store' => [
        'controller' => AcceuilController::class,
        'middlewares' => ['auth'],
        'action' => 'storeCompteSecondaire',
        'method' => 'POST'
    ],
    'GET:/transactions/create' => [
        'controller' => AcceuilController::class,
        'middlewares' => ['auth'],
        'action' => 'createTransaction',
        'method' => 'GET'
    ],
    'POST:/transactions/store' => [
        'controller' => AcceuilController::class,
        'middlewares' => ['auth'],
        'action' => 'storeTransaction',
        'method' => 'POST'
    ],
    'GET:/inscription' => [
        'controller' => SecurityController::class,
        'action' => 'create',
        'method' => 'GET'
    ],
    'POST:/inscription' => [
        'controller' => SecurityController::class,
        'action' => 'store',
        'method' => 'POST',
        'middlewares' => ['PasswordHashMiddleware']
    ]
];
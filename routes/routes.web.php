<?php
use App\Controllers\AcceuilController;
use App\Controllers\SecurityController;
use App\Controllers\CompteController;
use App\Controllers\TransactionController;

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
        'controller' => TransactionController::class,
        'middlewares' => ['auth'],
        'action' => 'index',
        'method' => 'GET'
    ],
    '/comptes' => [
        'controller' => CompteController::class,
        'middlewares' => ['auth'],
        'action' => 'index',
        'method' => 'GET'
    ],
    'GET:/comptes/secondaire/create' => [
        'controller' => CompteController::class,
        'middlewares' => ['auth'],
        'action' => 'create',
        'method' => 'GET'
    ],
    'POST:/comptes/secondaire/store' => [
        'controller' => CompteController::class,
        'middlewares' => ['auth'],
        'action' => 'store',
        'method' => 'POST'
    ],
    'GET:/transactions/create' => [
        'controller' => TransactionController::class,
        'middlewares' => ['auth'],
        'action' => 'create',
        'method' => 'GET'
    ],
    'POST:/transactions/store' => [
        'controller' => TransactionController::class,
        'middlewares' => ['auth'],
        'action' => 'store',
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
    ],
    'GET:/comptes/change-principal' => [
        'controller' => CompteController::class,
        'middlewares' => ['auth'],
        'action' => 'changePrincipal',
        'method' => 'GET'
    ],
    'POST:/comptes/change-principal/store' => [
        'controller' => CompteController::class,
        'middlewares' => ['auth'],
        'action' => 'storeChangePrincipal',
        'method' => 'POST'
    ],
];
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
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
    '/inscription' => [
        'controller' => SecurityController::class,
        'action' => 'store'
    ],
    // '/accueil' => [
    //     'controller' => AcceuilController::class,
    //     'action' => 'index'
    // ]
];
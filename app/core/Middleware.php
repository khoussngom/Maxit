<?php

namespace App\Core;

class Middleware
{
    public static function handle(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            switch ($middleware) {
                case 'auth':
                    self::auth();
                    break;
                case 'isCommercial':
                    self::isCommercial();
                    break;
                case 'isClient':
                    self::isClient();
                    break;
            }
        }
    }
    private static function auth()
    {
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if (empty($_SESSION['user'])) {
            header('Location: ' . getenv('BASE_URL') . '/login');
            exit;
        }
    }
    private static function isCommercial()
    {
        if (empty($_SESSION['user']) || strtolower($_SESSION['user']['type']) !== 'commercial') {
            http_response_code(403);
            echo "Accès interdit : réservé aux commerciaux.";
            exit;
        }
    }
    private static function isClient()
    {
        if (empty($_SESSION['user']) || strtolower($_SESSION['user']['type']) !== 'client') {
            http_response_code(403);
            echo "Accès interdit : réservé aux clients.";
            exit;
        }
    }

}
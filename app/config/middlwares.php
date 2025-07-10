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
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
    }

    private static function isCommercial()
    {
        if (strtoupper($_SESSION['user']['type'] ) !== 'client') {
            http_response_code(403);
            echo "Accès interdit : réservé aux commercial.";
            exit;
        }
    }

    private static function isClient()
    {
        if ($_SESSION['user']['type'] !== 'Commercial') {
            http_response_code(403);
            echo "Accès interdit : réservé aux clients.";
            exit;
        }
    }
}
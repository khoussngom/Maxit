<?php
namespace App\Middlewares;
class Auth
{
    public function __invoke()
    {
        session_start();
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}



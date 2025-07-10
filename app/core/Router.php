<?php

namespace App\Core;

class Router
{
    public static function resolve()
    {
        
        $routes = require_once '../routes/routes.web.php';

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (array_key_exists($uri, $routes)) {
            $route = $routes[$uri];
            $controller = $route['controller'];
            $action = $route['action'];


            if (!empty($route['middlewares'])) {
                Middleware::handle($route['middlewares']);
            }
            
                $controllerInstance = new $controller();
                $controllerInstance->$action();

        } else {
            http_response_code(404);
            echo "Page not found.";
        }
    }
}
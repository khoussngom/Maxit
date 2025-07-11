<?php

namespace App\Core;

use App\Core\App;

class Router
{
    protected static $routes;
    
    /**
     * Charge les routes depuis le fichier routes.web.php
     */
    public static function loadRoutes()
    {
        if (static::$routes === null) {
            $routesFile = dirname(__DIR__, 2) . '/routes/routes.web.php';
            
            if (file_exists($routesFile)) {
                error_log("Chargement des routes depuis: $routesFile");
                static::$routes = require $routesFile;
            } else {
                error_log("Fichier de routes non trouvé: $routesFile");
                static::$routes = [];
            }
        }
        
        return static::$routes;
    }
    
    public static function resolve()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        
        $uri = parse_url($uri, PHP_URL_PATH);
        
        error_log("Résolution de route: $method $uri");
        
        // Charger les routes depuis le fichier externe
        $routes = static::loadRoutes();
        
        if (isset($routes[$uri])) {
            $route = $routes[$uri];
            $controllerClass = $route['controller'];
            $action = $route['action'];
            
            error_log("Route trouvée: $controllerClass@$action");
            
            // Vérifier et appliquer les middlewares
            if (isset($route['middlewares']) && is_array($route['middlewares'])) {
                foreach ($route['middlewares'] as $middleware) {
                    self::applyMiddleware($middleware);
                }
            }
            
            $controllerInstance = new $controllerClass();
            return $controllerInstance->$action();
        }
        
        error_log("Route non trouvée: $method $uri");
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }
    
    /**
     * Applique un middleware spécifique
     * 
     * @param string $middlewareName Nom du middleware à appliquer
     * @return void
     */
    protected static function applyMiddleware($middlewareName)
    {
        $app = App::getInstance();
        $session = $app->getDependency('session');
        
        switch ($middlewareName) {
            case 'auth':
                // Vérifier si l'utilisateur est connecté
                if (!$session->get('logged_in')) {
                    error_log("Middleware auth: Utilisateur non connecté, redirection vers /login");
                    header('Location: /login');
                    exit;
                }
                break;
                
            case 'admin':
                // Vérifier si l'utilisateur est un administrateur
                if (!$session->get('logged_in') || $session->get('user_type') !== 'admin') {
                    error_log("Middleware admin: Accès non autorisé, redirection vers /");
                    header('Location: /');
                    exit;
                }
                break;
                
            // Ajoutez d'autres middlewares selon vos besoins
        }
    }
}
<?php
// Définir une limite de temps d'exécution plus longue uniquement pour l'environnement de développement
ini_set('max_execution_time', 60); // 60 secondes

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/core/env.php';
require_once __DIR__ . '/../app/core/Router.php';
loadEnv(__DIR__ . '/../.env');

use App\Core\Router;

Router::resolve();

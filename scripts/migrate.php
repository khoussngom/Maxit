<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/bootstrap.php';

use App\Migrations\Migration;

echo "Lancement de la migration...\n";

$migration = new Migration();
$result = $migration->run();

if ($result) {
    echo "Migration terminée avec succès.\n";
} else {
    echo "Échec de la migration.\n";
    exit(1);
}

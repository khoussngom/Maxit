<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/bootstrap.php';

use App\Seeders\Seeder;

echo "Lancement du seeder...\n";

$seeder = new Seeder();
$result = $seeder->run();

if ($result) {
    echo "Seeder terminé avec succès.\n";
} else {
    echo "Échec du seeder.\n";
    exit(1);
}

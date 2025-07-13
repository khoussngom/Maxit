<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();

// Récupérer tous les comptes pour inspection
$stmt = $db->query('SELECT * FROM compte LIMIT 5');
$comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Afficher la structure complète des comptes
echo "Structure des comptes:\n";
foreach ($comptes as $index => $compte) {
    echo "Compte #" . ($index + 1) . ":\n";
    print_r($compte);
    echo "\n";
}

// Obtenir les noms des colonnes de la table compte
echo "Colonnes de la table compte:\n";
$stmt = $db->query('SELECT column_name, data_type FROM information_schema.columns WHERE table_name = \'compte\'');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($columns);

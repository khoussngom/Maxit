<?php
// Fichier pour inspecter la table compte

// Inclure les dépendances
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/bootstrap.php';

// Connexion directe à la base de données
$host = getenv('DB_HOST') ?? 'localhost';
$port = getenv('DB_PORT') ?? '5432';
$dbname = getenv('DB_NAME') ?? 'maxit';
$username = getenv('DB_USER') ?? 'postgres';
$password = getenv('DB_PASS') ?? 'Marakhib';

try {
    echo "Tentative de connexion à la base de données...\n";
    
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Connexion réussie!\n\n";
    
    // Récupérer la structure de la table compte
    echo "Structure de la table compte:\n";
    $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns 
                         WHERE table_name = 'compte' ORDER BY ordinal_position");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- " . $column['column_name'] . " (" . $column['data_type'] . ")\n";
    }
    echo "\n";
    
    // Récupérer quelques données
    echo "Échantillon de données de la table compte:\n";
    $stmt = $pdo->query("SELECT * FROM compte LIMIT 5");
    $comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($comptes as $index => $compte) {
        echo "Compte #" . ($index + 1) . ":\n";
        foreach ($compte as $key => $value) {
            echo "  $key: " . (is_null($value) ? "NULL" : $value) . "\n";
        }
        echo "\n";
    }
    
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage() . "\n";
    exit(1);
}

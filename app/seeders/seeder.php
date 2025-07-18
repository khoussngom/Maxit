<?php

namespace App\Seeders;

use App\Core\Database;
use App\Core\App;

class Seeder
{
    private \PDO $pdo;

    public function __construct()
    {
        $app = App::getInstance();
        $this->pdo = $app->getDependency('db');
    }

    public function run(): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // Seeder les personnes
            $this->seedPersonnes();
            
            // Seeder les comptes
            $this->seedComptes();
            
            // Seeder les transactions
            $this->seedTransactions();
            
            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors du seeding: " . $e->getMessage());
            return false;
        }
    }

    private function seedPersonnes(): void
    {
        // Vérifier si des personnes existent déjà
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM personne");
        if ((int)$stmt->fetchColumn() > 0) {
            error_log("Des personnes existent déjà, pas besoin de seeder");
            return;
        }

        // Création d'un commercial
        $commercialData = [
            'telephone' => '770000000',
            'numero_identite' => 'COM123456',
            'photoRecto' => 'default.jpg',
            'photoVerso' => 'default.jpg',
            'prenom' => 'Admin',
            'nom' => 'Commercial',
            'adresse' => 'Dakar, Sénégal',
            'typePersonne' => 'commercial',
            'login' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT)
        ];
        
        $this->insertPersonne($commercialData);
        error_log("Commercial ajouté: " . $commercialData['telephone']);

        // Création de 5 clients
        $clients = [
            [
                'telephone' => '771111111',
                'numero_identite' => 'CLI123456',
                'prenom' => 'John',
                'nom' => 'Doe'
            ],
            [
                'telephone' => '772222222',
                'numero_identite' => 'CLI234567',
                'prenom' => 'Jane',
                'nom' => 'Smith'
            ],
            [
                'telephone' => '773333333',
                'numero_identite' => 'CLI345678',
                'prenom' => 'Alice',
                'nom' => 'Johnson'
            ],
            [
                'telephone' => '774444444',
                'numero_identite' => 'CLI456789',
                'prenom' => 'Bob',
                'nom' => 'Brown'
            ],
            [
                'telephone' => '775555555',
                'numero_identite' => 'CLI567890',
                'prenom' => 'Charlie',
                'nom' => 'Davis'
            ]
        ];

        foreach ($clients as $client) {
            $clientData = [
                'telephone' => $client['telephone'],
                'numero_identite' => $client['numero_identite'],
                'photoRecto' => 'default.jpg',
                'photoVerso' => 'default.jpg',
                'prenom' => $client['prenom'],
                'nom' => $client['nom'],
                'adresse' => 'Dakar, Sénégal',
                'typePersonne' => 'client',
                'login' => strtolower($client['prenom']),
                'password' => password_hash('password123', PASSWORD_DEFAULT)
            ];
            
            $this->insertPersonne($clientData);
            error_log("Client ajouté: " . $clientData['telephone']);
        }
    }

    private function insertPersonne(array $data): void
    {
        $columns = implode('", "', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO personne (\"$columns\") VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    private function seedComptes(): void
    {
        // Vérifier si des comptes existent déjà
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM compte");
        if ((int)$stmt->fetchColumn() > 0) {
            error_log("Des comptes existent déjà, pas besoin de seeder");
            return;
        }

        // Récupérer tous les clients
        $stmt = $this->pdo->query("SELECT telephone FROM personne WHERE typePersonne = 'client'");
        $clients = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($clients as $telephone) {
            // Compte principal
            $this->insertCompte([
                'telephone' => 'C' . substr($telephone, 1),
                'solde' => 100000, // 100 000 FCFA de départ
                'personne_telephone' => $telephone,
                'typecompte' => 'principal'
            ]);
            error_log("Compte principal ajouté pour: " . $telephone);

            // Compte secondaire (pour certains clients)
            if (rand(0, 1) === 1) {
                $this->insertCompte([
                    'telephone' => 'S' . substr($telephone, 1),
                    'solde' => 50000, // 50 000 FCFA de départ
                    'personne_telephone' => $telephone,
                    'typecompte' => 'secondaire'
                ]);
                error_log("Compte secondaire ajouté pour: " . $telephone);
            }
        }
    }

    private function insertCompte(array $data): void
    {
        $columns = implode('", "', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO compte (\"$columns\") VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    private function seedTransactions(): void
    {
        // Vérifier si des transactions existent déjà
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM transactions");
        if ((int)$stmt->fetchColumn() > 0) {
            error_log("Des transactions existent déjà, pas besoin de seeder");
            return;
        }

        // Récupérer tous les comptes
        $stmt = $this->pdo->query("SELECT telephone FROM compte");
        $comptes = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Types de transactions
        $types = ['depot', 'retrait', 'paiement'];
        
        // Générer des transactions aléatoires
        foreach ($comptes as $compteTelephone) {
            $nbTransactions = rand(3, 10);
            
            for ($i = 0; $i < $nbTransactions; $i++) {
                $type = $types[array_rand($types)];
                $montant = rand(1000, 50000);
                $date = date('Y-m-d', strtotime('-' . rand(1, 30) . ' days'));
                $motif = "Transaction " . ucfirst($type) . " #" . rand(1000, 9999);
                
                $transactionData = [
                    'montant' => $montant,
                    'compte_telephone' => $compteTelephone,
                    'type' => $type,
                    'date' => $date,
                    'motif' => $motif,
                    'etat' => 'completed'
                ];
                
                // Ajouter source/destination pour certains types
                if ($type === 'paiement') {
                    $transactionData['destination_telephone'] = $comptes[array_rand($comptes)];
                }
                
                $this->insertTransaction($transactionData);
                error_log("Transaction ajoutée: " . $type . " pour " . $compteTelephone);
            }
        }
    }

    private function insertTransaction(array $data): void
    {
        $columns = implode('", "', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO transactions (\"$columns\") VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }
}

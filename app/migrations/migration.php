<?php

namespace App\Migrations;

use App\Core\App;
use App\Core\Database;

class Migration
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
            
            $this->createTypes();
            
            $this->createPersonneTable();
            $this->createCompteTable();
            $this->createTransactionsTable();
            
            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors de la migration: " . $e->getMessage());
            return false;
        }
    }

    private function createTypes(): void
    {
        $stmt = $this->pdo->query("SELECT 1 FROM pg_type WHERE typname = 'type_compte'");
        if (!$stmt->fetch()) {
            $this->pdo->exec("
                CREATE TYPE type_compte AS ENUM (
                    'principal',
                    'secondaire'
                )
            ");
            error_log("Type ENUM type_compte créé");
        }

        $stmt = $this->pdo->query("SELECT 1 FROM pg_type WHERE typname = 'type_personne'");
        if (!$stmt->fetch()) {
            $this->pdo->exec("
                CREATE TYPE type_personne AS ENUM (
                    'client',
                    'commercial'
                )
            ");
            error_log("Type ENUM type_personne créé");
        }

        $stmt = $this->pdo->query("SELECT 1 FROM pg_type WHERE typname = 'type_transaction'");
        if (!$stmt->fetch()) {
            $this->pdo->exec("
                CREATE TYPE type_transaction AS ENUM (
                    'depot',
                    'retrait',
                    'paiement',
                    'annulation'
                )
            ");
            error_log("Type ENUM type_transaction créé (avec ajout du type 'annulation')");
        }
    }

    private function createPersonneTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS personne (
                telephone VARCHAR(20) NOT NULL,
                numero_identite VARCHAR(50) NOT NULL,
                photoRecto TEXT,
                photoVerso TEXT,
                prenom VARCHAR(100),
                nom VARCHAR(100),
                adresse TEXT,
                typePersonne VARCHAR(50),
                login VARCHAR(100),
                password VARCHAR(100),
                PRIMARY KEY (telephone),
                UNIQUE (numero_identite),
                UNIQUE (telephone)
            )
        ");
        error_log("Table personne créée");
    }

    private function createCompteTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS compte (
                telephone VARCHAR(20) NOT NULL,
                solde DOUBLE PRECISION,
                personne_telephone VARCHAR(20) NOT NULL,
                typecompte type_compte,
                PRIMARY KEY (telephone),
                FOREIGN KEY (personne_telephone) REFERENCES personne(telephone) ON DELETE CASCADE
            )
        ");
        error_log("Table compte créée");
    }

    private function createTransactionsTable(): void
    {
        $stmt = $this->pdo->query("SELECT 1 FROM pg_class WHERE relname = 'transactions_id_seq'");
        if (!$stmt->fetch()) {
            $this->pdo->exec("
                CREATE SEQUENCE transactions_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1
            ");
            error_log("Séquence transactions_id_seq créée");
        }

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS transactions (
                id INTEGER NOT NULL DEFAULT nextval('transactions_id_seq'),
                montant DOUBLE PRECISION NOT NULL,
                compte_telephone VARCHAR(20) NOT NULL,
                type type_transaction NOT NULL,
                date DATE DEFAULT CURRENT_DATE,
                motif TEXT,
                destination_telephone VARCHAR(20),
                source_telephone VARCHAR(20),
                etat VARCHAR(20) DEFAULT 'completed',
                PRIMARY KEY (id),
                FOREIGN KEY (compte_telephone) REFERENCES compte(telephone) ON DELETE CASCADE
            )
        ");
        error_log("Table transactions créée");
    }
}

<?php
namespace App\Core;

use App\Services\SecurityService;
use App\Repository\CompteRepository;
use App\Repository\PersonneRepository;

class App
{
    private static ?App $instance = null;
    private array $dependencies = [];
    private bool $isInitializingDependencies = false;
    
    private function __construct()
    {
        $this->initDependencies();
    }
    
    public static function getInstance(): App
    {
        if (self::$instance === null) {
            self::$instance = new App();
        }
        return self::$instance;
    }
    
    private function initDependencies(): void
    {

        if ($this->isInitializingDependencies) {
            return;
        }
        
        $this->isInitializingDependencies = true;
        
        try {

            try {
                $this->dependencies['db'] = Database::getInstance();
                error_log("Base de données initialisée avec succès");
            } catch (\Exception $e) {
                error_log("Erreur d'initialisation de la base de données: " . $e->getMessage());

            }
            

            $this->dependencies['session'] = Session::getInstance();
            

            if (isset($this->dependencies['db'])) {
                try {
                    $this->dependencies['personneRepository'] = new PersonneRepository();
                    $this->dependencies['compteRepository'] = new CompteRepository();
                } catch (\Exception $e) {
                    error_log("Erreur d'initialisation des repositories: " . $e->getMessage());
                }
            }
            

            if (isset($this->dependencies['personneRepository'])) {
                try {
                    $this->dependencies['security'] = new SecurityService();
                } catch (\Exception $e) {
                    error_log("Erreur d'initialisation du service de sécurité: " . $e->getMessage());
                }
            }
            

            error_log("Dépendances initialisées: " . implode(', ', array_keys($this->dependencies)));
            
        } catch (\Exception $e) {
            error_log("Erreur lors de l'initialisation des dépendances: " . $e->getMessage());
        } finally {
            $this->isInitializingDependencies = false;
        }
    }
    
    public function getDependency(string $name)
    {
        if (!isset($this->dependencies[$name])) {
            $availableDeps = implode(', ', array_keys($this->dependencies));
            error_log("Dépendance '$name' non trouvée. Dépendances disponibles: $availableDeps");
            throw new \Exception("Dépendance '$name' non trouvée. Dépendances disponibles: $availableDeps");
        }
        
        return $this->dependencies[$name];
    }
}




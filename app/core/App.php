<?php
namespace App\Core;

use App\Core\Upload;
use App\Core\Session;
use App\Core\Database;
use App\Services\CompteService;
use App\Services\EnvoyerMessage;
use App\Services\SecurityService;
use App\Repository\CompteRepository;
use App\Services\TransactionService;
use App\Repository\PersonneRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\Yaml\Yaml;

class App
{
    private static ?\App\Core\App $instance = null;
    private array $dependencies = [];
    private bool $isInitializingDependencies = false;
    private array $initQueue = [];
    
    private function __construct()
    {
        $this->initDependencies();
    }
    
    public static function getInstance(): \App\Core\App
    {
        if (self::$instance === null) {
            self::$instance = new \App\Core\App();
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
            // Essayer de charger les dépendances à partir du fichier services.yml
            $servicesFile = dirname(__DIR__) . '/config/services.yml';
            if (file_exists($servicesFile)) {
                error_log("Chargement des services depuis: " . $servicesFile);
                $this->loadServicesFromYaml($servicesFile);
            } else {
                error_log("Fichier services.yml non trouvé, utilisation de la configuration par défaut");
                $this->loadDefaultDependencies();
            }
        } catch (\Exception $e) {
            error_log("Erreur lors du chargement des services: " . $e->getMessage());
            error_log("Utilisation de la configuration par défaut");
            $this->loadDefaultDependencies();
        }
        
        $this->isInitializingDependencies = false;
    }
    
    private function loadServicesFromYaml(string $servicesFile): void
    {
        try {
            $services = Yaml::parseFile($servicesFile);
            
            if (!isset($services['services']) || !is_array($services['services'])) {
                throw new \Exception("Format du fichier services.yml invalide");
            }
            
            // Remplacer les paramètres
            $parameters = [];
            if (isset($services['parameters']) && is_array($services['parameters'])) {
                $parameters = $services['parameters'];
            } else {
                // Paramètres par défaut pour la base de données
                $parameters = [
                    'db.dsn' => getenv('DB_DSN'),
                    'db.username' => getenv('DB_USERNAME'),
                    'db.password' => getenv('DB_PASSWORD')
                ];
            }
            
            // Créer les services dans l'ordre de déclaration
            foreach ($services['services'] as $name => $serviceConfig) {
                $this->createService($name, $serviceConfig, $parameters);
            }
            
            error_log("Services chargés avec succès depuis le fichier YAML");
        } catch (\Exception $e) {
            error_log("Erreur lors du chargement des services depuis le YAML: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function createService(string $name, array $serviceConfig, array $parameters): void
    {
        // Vérifier si le service existe déjà
        if (isset($this->dependencies[$name])) {
            return;
        }
        
        if (!isset($serviceConfig['class'])) {
            throw new \Exception("Class non définie pour le service: $name");
        }
        
        $className = $serviceConfig['class'];
        $arguments = $serviceConfig['arguments'] ?? [];
        
        // Résoudre les arguments
        $resolvedArguments = [];
        foreach ($arguments as $argument) {
            if (is_string($argument) && strpos($argument, '@') === 0) {
                // Référence à un autre service
                $serviceName = substr($argument, 1);
                if (!isset($this->dependencies[$serviceName])) {
                    // Si le service n'existe pas encore, le créer récursivement
                    if (isset($services['services'][$serviceName])) {
                        $this->createService($serviceName, $services['services'][$serviceName], $parameters);
                    } else {
                        throw new \Exception("Service référencé non trouvé: $serviceName");
                    }
                }
                $resolvedArguments[] = $this->dependencies[$serviceName];
            } elseif (is_string($argument) && strpos($argument, '%') === 0 && substr($argument, -1) === '%') {
                // Paramètre
                $paramName = substr($argument, 1, -1);
                $resolvedArguments[] = $parameters[$paramName] ?? null;
            } else {
                // Valeur directe
                $resolvedArguments[] = $argument;
            }
        }
        
        // Créer l'instance
        try {
            if ($className === 'PDO') {
                // Cas spécial pour PDO
                $this->dependencies[$name] = new \PDO(...$resolvedArguments);
            } else {
                $reflection = new \ReflectionClass($className);
                $this->dependencies[$name] = $reflection->newInstanceArgs($resolvedArguments);
            }
            error_log("Service '$name' créé avec succès");
        } catch (\Exception $e) {
            error_log("Erreur lors de la création du service '$name': " . $e->getMessage());
            throw $e;
        }
    }
    
    private function loadDefaultDependencies(): void
    {
        try {
            try {
                $this->dependencies['db'] = Database::getInstance();
                error_log("Base de données initialisée avec succès");
            } catch (\Exception $e) {
                error_log("Erreur d'initialisation de la base de données: " . $e->getMessage());
            }
            
            $this->dependencies['session'] = Session::getInstance();
            $this->dependencies['envoyerMessage'] = EnvoyerMessage::getInstance();
            $this->dependencies['upload'] = new Upload();
            

            if (isset($this->dependencies['db'])) {
                $pdo = $this->dependencies['db'];
                try {
                    $this->dependencies['personneRepository'] = new PersonneRepository();
                    error_log("PersonneRepository initialisé avec succès");
                } catch (\Exception $e) {
                    error_log("Erreur d'initialisation de PersonneRepository: " . $e->getMessage());
                }
                
                try {
                    $this->dependencies['compteRepository'] = new CompteRepository();
                    error_log("CompteRepository initialisé avec succès");
                } catch (\Exception $e) {
                    error_log("Erreur d'initialisation de CompteRepository: " . $e->getMessage());
                }
                
                try {
                    $this->dependencies['transactionRepository'] = new TransactionRepository($pdo);
                    error_log("TransactionRepository initialisé avec succès");
                } catch (\Exception $e) {
                    error_log("Erreur d'initialisation de TransactionRepository: " . $e->getMessage());
                }
            }
            
            
            try {
                if (isset($this->dependencies['transactionRepository'])) {
                    $this->dependencies['transactionService'] = new TransactionService($this->dependencies['transactionRepository']);
                    error_log("TransactionService initialisé avec succès");
                }
            } catch (\Exception $e) {
                error_log("Erreur d'initialisation de TransactionService: " . $e->getMessage());
            }
            
            try {
                if (isset($this->dependencies['compteRepository']) && isset($this->dependencies['transactionService'])) {
                    $this->dependencies['compteService'] = new CompteService(
                        $this->dependencies['compteRepository'],
                        $this->dependencies['transactionService']
                    );
                    error_log("CompteService initialisé avec succès");
                }
            } catch (\Exception $e) {
                error_log("Erreur d'initialisation de CompteService: " . $e->getMessage());
            }
            
            try {
                if (isset($this->dependencies['personneRepository'])) {
                    $this->dependencies['security'] = new SecurityService();
                    error_log("SecurityService initialisé avec succès");
                }
            } catch (\Exception $e) {
                error_log("Erreur d'initialisation du service de sécurité: " . $e->getMessage());
            }
            
            error_log("Toutes les dépendances ont été initialisées: " . implode(', ', array_keys($this->dependencies)));
            
        } catch (\Exception $e) {
            error_log("Erreur lors de l'initialisation des dépendances: " . $e->getMessage());
        } finally {
            $this->isInitializingDependencies = false;
        }
    }
    
    public function getDependency(string $name)
    {
        if (!isset($this->dependencies[$name])) {
            throw new \Exception("Dépendance '$name' non trouvée. Dépendances disponibles: " . implode(', ', array_keys($this->dependencies)));
        }
        
        if ($this->dependencies[$name] instanceof \Closure) {
            $this->dependencies[$name] = $this->dependencies[$name]();
        }
        
        return $this->dependencies[$name];
    }
}




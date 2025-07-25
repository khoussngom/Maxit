<?php
namespace App\Core;

use App\Core\Upload;
use App\Core\Session;
use App\Core\Database;
use App\Services\CompteService;
use App\Services\EnvoyerMessage;
use Symfony\Component\Yaml\Yaml;
use App\Services\SecurityService;
use App\Repository\CompteRepository;
use App\Services\TransactionService;
use App\Repository\PersonneRepository;
use App\Repository\TransactionRepository;

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
            
            // Initialiser les paramètres
            $parameters = [];
            
            // Charger les paramètres depuis le fichier services.yml
            if (isset($services['parameters']) && is_array($services['parameters'])) {
                $parameters = $services['parameters'];
            }
            
            // Charger les paramètres depuis un fichier parameters.yml séparé
            $parametersFile = dirname(__DIR__) . '/config/parameters.yml';
            if (file_exists($parametersFile)) {
                error_log("Chargement des paramètres depuis: " . $parametersFile);
                try {
                    $parametersData = Yaml::parseFile($parametersFile);
                    if (isset($parametersData['parameters']) && is_array($parametersData['parameters'])) {
                        // Fusionner les paramètres, ceux de parameters.yml ayant la priorité
                        $parameters = array_merge($parameters, $parametersData['parameters']);
                    }
                } catch (\Exception $e) {
                    error_log("Erreur lors du chargement des paramètres: " . $e->getMessage());
                }
            }
            
            // Fallback sur les variables d'environnement si nécessaire
            if (empty($parameters)) {
                $parameters = [
                    'db.dsn' => getenv('DB_DSN') ?: 'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME'),
                    'db.username' => getenv('DB_USER'),
                    'db.password' => getenv('DB_PASS')
                ];
            }
            
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
        if (isset($this->dependencies[$name])) {
            return;
        }
        
        if (!isset($serviceConfig['class'])) {
            throw new \Exception("Class non définie pour le service: $name");
        }
        
        $className = $serviceConfig['class'];
        $arguments = $serviceConfig['arguments'] ?? [];
        
        $resolvedArguments = [];
        foreach ($arguments as $argument) {
            if (is_string($argument) && strpos($argument, '@') === 0) {
                // C'est une référence à un autre service
                $serviceName = substr($argument, 1);
                if (!isset($this->dependencies[$serviceName])) {
                    // Le service n'existe pas encore, nous devons d'abord le créer
                    // Essayons de le charger par défaut
                    try {
                        // Essayer de charger un service par défaut
                        if (method_exists($this, 'loadDefaultDependencies')) {
                            $this->loadDefaultDependencies();
                        }
                    } catch (\Exception $e) {
                        throw new \Exception("Service référencé non trouvé: $serviceName");
                    }
                }
                $resolvedArguments[] = $this->dependencies[$serviceName];
            } elseif (is_string($argument) && strpos($argument, '%') === 0 && substr($argument, -1) === '%') {
                // C'est un paramètre
                $paramName = substr($argument, 1, -1);
                if (isset($parameters[$paramName])) {
                    $resolvedArguments[] = $this->resolveParameter($parameters[$paramName]);
                } else {
                    throw new \Exception("Paramètre non trouvé: $paramName");
                }
            } else {

                $resolvedArguments[] = $argument;
            }
        }
        

        try {
            if ($className === 'PDO') {

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
                if (isset($this->dependencies['personneRepository']) && isset($this->dependencies['db'])) {
                    $this->dependencies['security'] = new SecurityService();
                    error_log("SecurityService initialisé avec succès");
                } else {
                    error_log("Impossible d'initialiser SecurityService : dépendances manquantes");
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
    
    /**
     * Résout les valeurs des paramètres en remplaçant les références aux variables d'environnement
     */
    private function resolveParameter(string $value): string
    {
        // Vérifier si c'est une référence à une variable d'environnement
        if (preg_match('/%env\((.*?)\)%/', $value, $matches)) {
            $envName = $matches[1];
            $envValue = getenv($envName);
            
            if ($envValue === false) {
                error_log("Variable d'environnement non définie: $envName");
                return '';
            }
            
            // Remplacer la référence par la valeur réelle
            return str_replace("%env($envName)%", $envValue, $value);
        }
        
        return $value;
    }
}

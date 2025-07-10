<?php
use App\Core\Router;
use App\Core\Database;
use App\Abstract\AbstractRepository;

class App
{
    private static ?App $instance = null;
    private array $dependencies = [
        "core" => [
            "router" => null,
            "database" => null,
        ],
        "services" => [],
        "repositories" => []
    ];

    private function __construct()
    {
        $this->dependencies["core"]["router"] = new Router();
        $this->dependencies["core"]["database"] = Database::getInstance();
    }

    public static function getInstance(): App
    {
        if (self::$instance === null) {
            self::$instance = new App();
        }
        return self::$instance;
    }

    public function getDependency(string $key)
    {
        foreach ($this->dependencies as $group) {
            if (isset($group[$key])) {
                return $group[$key];
            }
        }
        return null;
    }
}




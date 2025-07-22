<?php
namespace App\Abstract;
use App\Core\Session;
abstract class AbstractController{

            
    protected ?string $layout;
    protected Session $session;


    public function __construct($layout = null)
    {
        $this->session = Session::getInstance();
        $this->layout = $layout ?? dirname(__DIR__, 3) . '/templates/layout/sidebar-main.layout.php';
    }

    abstract public  function index(): void;
    abstract public function create(): void;
    abstract public function store(): void;
    abstract public function update(): void;
    abstract public function show(): void;
    abstract public function edit(): void;
    abstract public function destroy();

    public function renderHtml(string $view, $data = [])
    {
        $baseUrl = getenv('BASE_URL') ?: '';
        
        $data['baseUrl'] = $baseUrl;
        
        if ($this->layout && file_exists($this->layout) && !isset($data['title'])) {
            $data['title'] = 'Maxit - Plateforme de services financiers';
        }
        
        extract($data);
        
        ob_start();
        require_once dirname(__DIR__, 3) . '/templates/' . $view . '.html.php';
        $contentForLayout = ob_get_clean();
        
        if ($this->layout && file_exists($this->layout)) {
            error_log("Utilisation du layout: " . $this->layout . " pour la vue: " . $view);
            ob_start();
            require_once $this->layout;
            $output = ob_get_clean();
        } else {
            error_log("Pas de layout utilisé pour la vue: " . $view);
            $output = $contentForLayout;
        }
        
        echo $output;
    }

    public function requireAuth(): void
    {
        session_start();
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    public function deconnexion(): void
    {
        session_destroy();
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}
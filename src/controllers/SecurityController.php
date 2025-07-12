<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Upload;
use App\Core\Validator;
use App\Abstract\AbstractController;
use App\Middlewares\PasswordHashMiddleware;

class SecurityController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create(): void
    {
        
        $app = App::getInstance();
        $session = $app->getDependency('session');
        
        $old = $session->get('old_input') ?? [];
        $errors = $session->get('flash_errors') ?? [];
        
    
        if (method_exists($session, 'set')) {
            $session->set('old_input', null);
            $session->set('flash_errors', null);
        } else {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            unset($_SESSION['old_input'], $_SESSION['flash_errors']);
        }
        

        $templatePath = dirname(__DIR__, 2) . '/templates/inscription.html.php';
        

        $this->renderHtml('inscription', [
            'old' => $old,
            'errors' => $errors
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $app = App::getInstance();
                $securityService = $app->getDependency('security');
                $session = $app->getDependency('session');
                
                $login = $_POST['login'] ?? '';
                $password = $_POST['password'] ?? '';
                
                if (empty($login) || empty($password)) {

                    $this->renderHtml('login', [
                        'error' => 'Veuillez remplir tous les champs',
                        'old' => ['login' => $login]
                    ]);
                    return;
                }
                
                $personne = $securityService->seConnecter($login, $password);
        
                if ($personne) {
                    $session->set('user', $personne);
                    $session->set('user_id', $personne->getTelephone());
                    $session->set('user_type', $personne->getTypePersonne());
                    $session->set('logged_in', true);
                    
                    if ($personne->getTypePersonne() === 'admin') {
                        header('Location: /admin');
                    } else {
                        header('Location: /accueil');
                    }
                    exit;
                } else {

                    $this->renderHtml('login', [
                        'error' => 'Login ou mot de passe incorrect',
                        'old' => ['login' => $login]
                    ]);
                }
            } catch (\Exception $e) {

                $this->renderHtml('login', [
                    'error' => 'Une erreur est survenue. Veuillez réessayer plus tard.'
                ]);
            }
        } else {

            $this->renderHtml('login');
        }
    }

    public function store(): void 
    {
        $formData = [
            'login' => trim($_POST['login'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'nom' => trim($_POST['nom'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'numero_identite' => trim($_POST['numeroIdentite'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'typePersonne' => 'client',
            'photoRecto' => null,
            'photoVerso' => null
        ];

        
        $photoRecto = Upload::save($_FILES['photorecto'] ?? null, 'uploads/cni');
        $photoVerso = Upload::save($_FILES['photoverso'] ?? null, 'uploads/cni');

        if (!$photoRecto) {
            Validator::addError('photorecto', 'La photo recto de la CNI est obligatoire');
        } else {
            $formData['photoRecto'] = $photoRecto;
        }

        if (!$photoVerso) {
            Validator::addError('photoverso', 'La photo verso de la CNI est obligatoire');
        } else {
            $formData['photoVerso'] = $photoVerso;
        }

        if (empty($formData['login'])) {
            Validator::addError('login', 'Le login est obligatoire');
        }
        if (empty($formData['password'])) {
            Validator::addError('password', 'Le mot de passe est obligatoire');
        }
        if (empty($formData['telephone'])) {
            Validator::addError('telephone', 'Le téléphone est obligatoire');
        }
        if (empty($formData['numero_identite'])) {
            Validator::addError('numeroIdentite', 'Le numéro CNI est obligatoire');
        }

        if (!Validator::isValid()) {
            $this->session->set('flash_errors', Validator::getErrors());
            $this->session->set('old_input', $formData);
            $this->renderHtml('inscription');
            return;
        }

        $app = App::getInstance();
        $securityService = $app->getDependency('security');

        // $formData = PasswordHashMiddleware::handle($formData);

        if ($securityService->inscrire($formData)) {
            $this->session->set('flash_success', 'Inscription réussie !');
            header('Location: ' . getenv('BASE_URL') . '/login');
            exit();
        }

        $this->session->set('flash_errors', Validator::getErrors());
        $this->session->set('old_input', $formData);
        $this->renderHtml('inscription');
    }

    public function show():void {}

    public function update():void {}

    public function edit():void {}
    
    public function destroy():void {}

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->login();
        } else {
            $this->renderHtml('login');
        }
    }

    public function logout()
    {
        $app = App::getInstance();
        $session = $app->getDependency('session');
        $session->destroy();
        header('Location: /');
        exit;
    }
    

    private function validateInscription(array $data): array
    {
        $errors = [];
        
        $requiredFields = [
            'login' => 'Le login est obligatoire',
            'password' => 'Le mot de passe est obligatoire',
            'prenom' => 'Le prénom est obligatoire',
            'nom' => 'Le nom est obligatoire',
            'telephone' => 'Le téléphone est obligatoire',
            'adresse' => 'L\'adresse est obligatoire',
            'numeroIdentite' => 'Le numéro d\'identité est obligatoire'
        ];
        
        foreach ($requiredFields as $field => $message) {
            if (empty($data[$field])) {
                $errors[$field] = $message;
            }
        }
        
        if (!empty($data['telephone']) && !preg_match('/^\d{9,15}$/', $data['telephone'])) {
            $errors['telephone'] = 'Le format du numéro de téléphone est invalide';
        }
        
        return $errors;
    }
}

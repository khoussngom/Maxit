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
                    // Stocker les informations de l'utilisateur dans la session
                    $session->set('user', $personne);
                    $session->set('user_id', $personne->getTelephone()); // Assurez-vous que cette ligne existe
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
        try {
            $app = App::getInstance();
            $session = $app->getDependency('session');

            $errors = $this->validateInscription($_POST);
            
            if (!empty($errors)) {

                $session->set('flash_errors', $errors);
                $session->set('old_input', $_POST);
                

                header('Location: /inscription');
                exit;
            }
            

            try {

                unset($_POST['debug']);
                

                $_POST['numero_identite'] = $_POST['numeroIdentite'] ?? null;
                unset($_POST['numeroIdentite']);
                

                $_POST['typePersonne'] = 'client';
                

                $data = \App\Middlewares\PasswordHashMiddleware::handle($_POST);
                

                if (isset($_FILES['photorecto']) && $_FILES['photorecto']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/identity/';
                    

                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileName = uniqid() . '_' . basename($_FILES['photorecto']['name']);
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['photorecto']['tmp_name'], $uploadFile)) {
                        $data['photoRecto'] = '/uploads/identity/' . $fileName;
                    }
                }
                
                if (isset($_FILES['photoverso']) && $_FILES['photoverso']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/identity/';
                    

                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileName = uniqid() . '_' . basename($_FILES['photoverso']['name']);
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['photoverso']['tmp_name'], $uploadFile)) {
                        $data['photoVerso'] = '/uploads/identity/' . $fileName;
                    }
                }
                

                $fieldsToExclude = ['debug', 'confirm_password'];
                foreach ($fieldsToExclude as $field) {
                    if (isset($data[$field])) {
                        unset($data[$field]);
                    }
                }
                                
                $securityService = $app->getDependency('security');
                $personneId = $securityService->inscrire($data, $app);
                
                if ($personneId) {

                    $session->set('flash_success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
                    header('Location: /login');
                    exit;
                } else {

                    $session->set('flash_errors', ['global' => 'Échec de l\'inscription. Veuillez réessayer.']);
                    $session->set('old_input', $_POST);
                    header('Location: /inscription');
                    exit;
                }
            } catch (\InvalidArgumentException $e) {

                $session->set('flash_errors', ['confirm_password' => $e->getMessage()]);
                $session->set('old_input', $_POST);
                header('Location: /inscription');
                exit;
            }
        } catch (\Exception $e) {
            
            $app = App::getInstance();
            $session = $app->getDependency('session');
            
            $session->set('flash_errors', ['global' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
            $session->set('old_input', $_POST);
            
            header('Location: /inscription');
            exit;
        }
    }

    public function show():void {}

    public function update():void {}

    public function edit():void {}
    
    public function destroy():void {}

    public function index():void
    {
        $this->renderHtml('login');
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

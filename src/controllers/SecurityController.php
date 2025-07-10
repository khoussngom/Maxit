<?php

namespace App\Controllers;

use App\Core\Upload;
use App\Core\Validator;
use App\Services\SecurityService;
use App\Abstract\AbstractController;

class SecurityController extends AbstractController
{

    private SecurityService $securityService;

    public function __construct()
    {
        parent::__construct();
        $this->securityService = new SecurityService();
    }

    public function create():void {}

    public function login()
    {
        if (empty($_POST)) {
            require dirname(__DIR__, 2) . '/templates/login.html.php';
            return;
        }

        $formData = [
            'login' => trim($_POST['login'] ?? ''),
            'password' => trim($_POST['password'] ?? '')
        ];


    
        if ($formData['login'] === '') {

            Validator::addError('login', 'Ce champ est obligatoire.');
        }
        if ($formData['password'] === '') {
            Validator::addError('password', 'Ce champ est obligatoire.');
        }

        if (!Validator::isValid()) {
            $this->session->set('flash_errors', Validator::getErrors());
            $this->session->set('old_input', $formData);
            require dirname(__DIR__, 2) . '/templates/login.html.php';
            return;
        }

        $user = $this->securityService->seConnecter($formData['login'], $formData['password']);

        if (!$user) {
            Validator::addError('global', 'Login ou mot de passe incorrect.');
            $this->session->set('flash_errors', Validator::getErrors());
            $this->session->set('old_input', $formData);
            require dirname(__DIR__, 2) . '/templates/login.html.php';
            return;
        }

        $this->session->set('user', $user);
        header('Location: ' . getenv('BASE_URL') . '/accueil');
        exit;
    }

    public function store():void
    {
        $formData = [
            'login' => trim($_POST['login'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'nom' => trim($_POST['nom'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'numeroIdentite' => trim($_POST['numeroIdentite'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'typePersonne' => trim($_POST['typePersonne'] ?? ''),
        ];

        Validator::isEmpty('login', $formData['login']);
        Validator::isEmpty('password', $formData['password']);
        Validator::isEmpty('prenom', $formData['prenom']);
        Validator::isEmpty('nom', $formData['nom']);
        Validator::isEmpty('adresse', $formData['adresse']);
        Validator::isEmpty('numeroIdentite', $formData['numeroIdentite']);
        Validator::isEmpty('telephone', $formData['telephone']);
        Validator::isEmpty('typePersonne', $formData['typePersonne']);
        Validator::isEmail('login', $formData['login']);

        $photoRecto = Upload::save($_FILES['photorecto'], __DIR__ . '/../../public/images/upload');
        $photoVerso = Upload::save($_FILES['photoverso'], __DIR__ . '/../../public/images/upload');

        if (!empty($photoRecto['error'])) {
            Validator::addError('photorecto', $photoRecto['error']);
        }
        if (!empty($photoVerso['error'])) {
            Validator::addError('photoverso', $photoVerso['error']);
        }

        if (!Validator::isValid()) {
            $this->session->set('flash_errors', Validator::getErrors());
            $this->session->set('old_input', $formData);
            require dirname(__DIR__, 2) . '/templates/inscription.html.php';
            return;
        }

        $formData['photorecto'] = $photoRecto['filename'] ?? null;
        $formData['photoverso'] = $photoVerso['filename'] ?? null;
        $formData['password'] = password_hash($formData['password'], PASSWORD_DEFAULT);

        $this->securityService->inscrire($formData);

        header('Location: /login');
        exit;
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
        $this->session->destroy();
        header('Location: /');
    }
    public function inscription(): void
    {
        require dirname(__DIR__, 2) . '/templates/inscription.html.php';
    }
}

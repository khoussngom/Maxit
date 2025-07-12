<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Upload;
use App\Core\Validator;
use App\Services\EnvoyerMessage;
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

    public function create(): void
    {
        $this->renderHtml('inscription');
    }

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


        if ($securityService->inscrire($formData)) {
            try {
                EnvoyerMessage::envoyerConfirmationInscription(
                    $formData['telephone'],
                    $formData['nom'],
                    $formData['prenom']
                );
                error_log("SMS de confirmation d'inscription envoyé à " . $formData['telephone']);
            } catch (\Exception $e) {
                error_log("Erreur lors de l'envoi du SMS de confirmation: " . $e->getMessage());
            }
            
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

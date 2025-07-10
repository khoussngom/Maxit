<?php
namespace App\Services;

use App\Core\Validator;
use App\Entity\PersonneEntity;
use App\Repository\PersonneRepository;

class SecurityService
{
    private PersonneRepository $personneRepository;

    public function __construct()
    {
        $this->personneRepository = new PersonneRepository();
    }


    public function seConnecter(string $login, string $password): ?PersonneEntity
    {
        $user = $this->personneRepository->findByLogin($login,$password);
        if ($user) {
            return $user;
        }
        return null;
    }



    public function checkUniqueFields(string $telephone, string $numeroIdentite): array
    {
        $errors = [];
        
        if ($this->personneRepository->findByTelephone($telephone)) {
            $errors['telephone'] = 'Ce numéro de téléphone est déjà utilisé';
        }
        
        if ($this->personneRepository->findByNumeroIdentite($numeroIdentite)) {
            $errors['numeroIdentite'] = 'Ce numéro CNI est déjà utilisé';
        }
        
        return $errors;
    }

    public function inscrire(array $data): bool
    {
        try {
            
            $result = $this->personneRepository->insert('personne', $data);
            return $result > 0;
        } catch (\PDOException $e) {
            error_log('Erreur insertion : ' . $e->getMessage());
            Validator::addError('global', 'Erreur lors de l\'inscription');
            return false;
        }
    }
}

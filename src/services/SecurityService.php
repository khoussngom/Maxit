<?php
namespace App\Services;

use App\Entity\PersonneEntity;
use App\Repository\PersonneRepository;

class SecurityService
{
    private PersonneRepository $personneRepository;

    public function __construct()
    {
        $this->personneRepository = new PersonneRepository();
    }

    public function inscrire(array $data): bool
    {
        return $this->personneRepository->insert('personne', $data) > 0;
    }

    public function seConnecter(string $login, string $password): ?PersonneEntity
    {
        $user = $this->personneRepository->findByLogin($login,$password);
        if ($user) {
            return $user;
        }
        return null;
    }
}
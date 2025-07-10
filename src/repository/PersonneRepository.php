<?php

namespace App\Repository;

use App\Entity\PersonneEntity;
use App\Abstract\AbstractRepository;

class PersonneRepository extends AbstractRepository
{
    public function findByLogin(string $login, string $password): ?PersonneEntity
    {
        $sql = "SELECT * FROM personne WHERE (login = :login OR telephone = :login) AND password = :password";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['login' => $login, 'password' => $password]);
        $data = $stmt->fetch();
        return $data ? PersonneEntity::toObject($data) : null;
    }

    public function findByTelephone(string $telephone): ?PersonneEntity
    {
        $sql = "SELECT * FROM personne WHERE telephone = :telephone";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['telephone' => $telephone]);
        $data = $stmt->fetch();
        return $data ? PersonneEntity::toObject($data) : null;
    }

    public function findByNumeroIdentite(string $numeroIdentite): ?PersonneEntity
    {
        $sql = "SELECT * FROM personne WHERE numero_identite = :numeroIdentite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numeroIdentite' => $numeroIdentite]);
        $data = $stmt->fetch();
        return $data ? PersonneEntity::toObject($data) : null;
    }
}

<?php

namespace App\Repository;

use App\Entity\PersonneEntity;
use App\Abstract\AbstractRepository;

class PersonneRepository extends AbstractRepository
{
    public function findByLogin(string $login,string $password): ?PersonneEntity
    {
        $sql = "SELECT * FROM personne WHERE login = :login AND password = :password";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['login' => $login,'password' => $password]);
        $data = $stmt->fetch();
        return $data ? PersonneEntity::toObject($data) : null;
    }
}

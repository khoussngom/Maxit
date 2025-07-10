<?php


namespace App\Repository;

use App\Entity\CompteEntity;
use App\Abstract\AbstractRepository;

class CompteRepository extends AbstractRepository
{
    public function findByPersonne(string $telephone): ?array
    {
        $sql = "SELECT * FROM compte WHERE personne_telephone = :telephone";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['telephone' => $telephone]);
        return $stmt->fetch();
    }
}
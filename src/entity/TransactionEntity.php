<?php

namespace App\Entity;

require_once __DIR__ . '/Enums.php';

use DateTime;

class Transaction
{
    private float $montant;
    private Compte $compte;
    private string $type;
    private DateTime $date;

    public function __construct(float $montant, Compte $compte, string $type)
    {
        if (!in_array($type, [TypeTransaction::DEPOT, TypeTransaction::RETRAIT, TypeTransaction::PAIEMENT])) {
            throw new \InvalidArgumentException("Type de transaction invalide");
        }
        $this->montant = $montant;
        $this->compte = $compte;
        $this->type = $type;
        $this->date = new DateTime();
    }

    public function getCompte(): Compte
    {
        return $this->compte;
    }

    public function setCompte(Compte $compte): void
    {
        $this->compte = $compte;
    }


    public function getMontant()
    {
        return $this->montant;
    }


    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }


    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
}

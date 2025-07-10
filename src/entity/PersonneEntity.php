<?php
namespace App\Entity;

use App\Abstract\AbstractEntity;


class PersonneEntity extends AbstractEntity
{
    private string $telephone;
    private string $numeroIdentite;
    private ?string $photorecto;
    private ?string $photoverso;
    private ?string $prenom;
    private ?string $nom;
    private ?string $adresse;
    private string $typePersonne;
    private ?string $login;
    private ?string $password;
    private array $comptes = [];

    public function __construct(string $telephone, string $numeroIdentite, string $typePersonne)
    {
        if (!in_array($typePersonne, [TypePersonne::CLIENT, TypePersonne::COMMERCIAL])) {
            throw new \InvalidArgumentException("Type de personne invalide");
        }
        $this->telephone = $telephone;
        $this->numeroIdentite = $numeroIdentite;
        $this->typePersonne = $typePersonne;
    }

    public function addCompte(Compte $compte): void
    {
        $this->comptes[] = $compte;
    }

    public function getTypePersonne(): string
    {
        return $this->typePersonne;
    }

    public function setTypePersonne(string $type): void
    {
        $this->typePersonne = $type;
    }


    public function getTelephone()
    {
        return $this->telephone;
    }


    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNumeroIdentite()
    {
        return $this->numeroIdentite;
    }
    public function setNumeroIdentite($numeroIdentite)
    {
        $this->numeroIdentite = $numeroIdentite;

        return $this;
    }

    public function getPhotorecto()
    {
        return $this->photorecto;
    }

    public function setPhotorecto($photorecto)
    {
        $this->photorecto = $photorecto;

        return $this;
    }

    public function getPhotoverso()
    {
        return $this->photoverso;
    }

    public function setPhotoverso($photoverso)
    {
        $this->photoverso = $photoverso;

        return $this;
    }


    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }


    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }


    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLogin()
    {
        return $this->login;
    }


    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public static function toObject(array $data): self
    {
        if (
            empty($data['telephone']) ||
            empty($data['numero_identite']) ||
            empty($data['typePersonne'])
        ) {
            throw new \InvalidArgumentException("Champs obligatoires manquants pour PersonneEntity::toObject");
        }

        $personne = new self(
            $data['telephone'],
            $data['numero_identite'],
            $data['typePersonne']
        );
        $personne->setPhotorecto($data['photorecto'] ?? null);
        $personne->setPhotoverso($data['photoverso'] ?? null);
        $personne->setPrenom($data['prenom'] ?? null);
        $personne->setNom($data['nom'] ?? null);
        $personne->setAdresse($data['adresse'] ?? null);
        $personne->setLogin($data['login'] ?? null);
        $personne->setPassword($data['password'] ?? null);
        return $personne;
    }

    public function toArray():array
    {
        return [
            'telephone'      => $this->telephone ?? null,
            'numero_identite' => $this->numeroIdentite ?? null,
            'photorecto'     => $this->photorecto ?? null,
            'photoverso'     => $this->photoverso ?? null,
            'prenom'         => $this->prenom ?? null,
            'nom'            => $this->nom ?? null,
            'adresse'        => $this->adresse ?? null,
            'typePersonne'   => $this->typePersonne ?? null,
            'login'          => $this->login ?? null,
            'password'       => $this->password ?? null,
            'comptes'     => $this->comptes ?? [],
        ];
    }
}

<?php

namespace App\Entity;

use App\Abstract\AbstractEntity;

class TransactionEntity extends AbstractEntity
{
    protected ?int $id = null;
    protected string $reference;
    protected float $montant;
    protected string $type;
    protected string $dateTransaction;
    protected int $compteId;
    protected ?string $description = null;
    

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'montant' => $this->montant,
            'type' => $this->type,
            'date_transaction' => $this->dateTransaction,
            'compte_id' => $this->compteId,
            'description' => $this->description
        ];
    }
    

    public static function toObject(array $data): self
    {
        $transaction = new self();
        
        $mapping = [
            'id' => 'id',
            'reference' => 'reference',
            'montant' => 'montant',
            'type' => 'type',
            'date_transaction' => 'dateTransaction',
            'compte_id' => 'compteId',
            'description' => 'description'
        ];
        
        $transformedData = [];
        foreach ($data as $key => $value) {
            if (isset($mapping[$key])) {
                $transformedData[$mapping[$key]] = $value;
            }
        }
        
        return $transaction->hydrate($transformedData);
    }
}
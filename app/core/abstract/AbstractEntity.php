<?php 

namespace App\Abstract;

abstract class AbstractEntity
{
    abstract public function toArray(): array;

    abstract public static function toObject(array $data);

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}

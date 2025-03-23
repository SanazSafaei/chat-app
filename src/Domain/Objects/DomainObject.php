<?php

namespace App\Domain\Objects;

abstract class DomainObject
{
    protected ?int $id;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    abstract public function jsonSerialize(): array;
    abstract public static function jsonDeserialize($values): DomainObject;
}

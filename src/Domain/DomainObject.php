<?php

namespace App\Domain;

abstract class DomainObject
{
    public abstract function jsonSerialize(): array;
    public abstract static function jsonDeserialize($values): DomainObject;
}
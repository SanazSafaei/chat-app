<?php

namespace App\Domain\Objects;

abstract class DomainObject
{
    abstract public function jsonSerialize(): array;
    abstract public static function jsonDeserialize($values): DomainObject;
}

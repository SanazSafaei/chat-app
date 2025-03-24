<?php

namespace App\Domain\Objects\Group;

use App\Domain\Objects\DomainObject;

interface GroupRepository
{
    public function findById(int $id): Group;

    public function findByUserId(int $userId): array;

    public function insert(DomainObject $domain): DomainObject;
}
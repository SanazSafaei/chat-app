<?php

namespace App\Domain\Objects\Group;

interface GroupRepository
{
    public function findById(int $id): Group;

    public function findByUserId(int $userId): array;
}
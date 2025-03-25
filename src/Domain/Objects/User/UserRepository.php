<?php

declare(strict_types=1);

namespace App\Domain\Objects\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    public function findUserOfUsername(string $username): User;

    public function updateField($field, $value, $id): void;
}

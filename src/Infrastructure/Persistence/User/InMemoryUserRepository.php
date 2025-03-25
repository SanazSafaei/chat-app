<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\Objects\User\User;
use App\Domain\Objects\User\UserNotFoundException;
use App\Domain\Objects\User\UserRepository;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;

class InMemoryUserRepository extends Repository implements UserRepository
{
    public function __construct(DBInterface $DB)
    {
        parent::__construct($DB);
        $this->createTable();
    }

    public function getFields(): array
    {
        return [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'username' => 'TEXT',
            'password' => 'TEXT',
            'first_name' => 'TEXT',
            'last_name' => 'TEXT',
            'email' => 'TEXT',
            'photo' => 'TEXT',
            'last_seen' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    public function getTableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $result = $this->PDO->query('SELECT * FROM users');
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = User::jsonDeserialize($row);
        }
        return $dtoArray;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): User
    {
        $result = $this->PDO->query("SELECT * FROM users WHERE id = $id");
        $result = $result->fetch();
        if (!isset($result) || !$result) {
            throw new UserNotFoundException();
        }

        return User::jsonDeserialize($result);
    }

    public function findUserOfUsername(string $username): User
    {
        $stmt = $this->PDO->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!isset($result) || !$result) {
            throw new UserNotFoundException();
        }

        return User::jsonDeserialize($result);
    }
}

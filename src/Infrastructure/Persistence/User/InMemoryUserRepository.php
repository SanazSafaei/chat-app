<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Repository;
use DateTime;

class InMemoryUserRepository extends Repository implements UserRepository
{
    /**
     * @param User[]|null $users
     */
    public function __construct(?array $users = null)
    {
        parent::__construct();

        $logger = new \Slim\Logger();
        $logger->log('info', __DIR__);

        $this->createTable();
        $now = new DateTime();
        $user = new User(2, "bill.gates", "Bill", "Gates", "test/photo", $now, $now, $now);
        $this->insert($user);

//        $this->myPDO->query('INSERT INTO users (id, username, first_name, last_name, photo, last_seen, created_at, updated_at) VALUES (2, "bill.gates", "Bill", "Gates", "test/photo", DateTime(), DateTime(), DateTime())')->execute();
    }

    public function getFields(): array
    {
        return [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'username' => 'TEXT',
            'first_name' => 'TEXT',
            'last_name' => 'TEXT',
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
        $result= $this->PDO->query('SELECT * FROM users');
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
        $result= $this->PDO->query("SELECT * FROM users WHERE id = {$id}");
        if (!isset($result)) {
            throw new UserNotFoundException();
        }

        return User::jsonDeserialize($result->fetch());
    }

}

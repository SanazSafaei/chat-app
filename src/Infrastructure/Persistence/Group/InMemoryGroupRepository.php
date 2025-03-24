<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Group;

use App\Domain\Objects\Group\Group;
use App\Domain\Objects\Group\GroupRepository;
use App\Domain\Objects\Message\Message;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;

class InMemoryGroupRepository extends Repository implements GroupRepository
{
    public function __construct(?DBInterface $DB = null)
    {
        parent::__construct($DB);
        $this->createTable();
    }

    public function getFields(): array
    {
        return [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'name' => 'TEXT',
            'photo' => 'TEXT',
            'description' => "TEXT",
            'created_by' => 'INT',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    public function getTableName(): string
    {
        return 'groups';
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $result = $this->PDO->query('SELECT * FROM groups');
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Message::jsonDeserialize($row);
        }
        return $dtoArray;
    }

    public function findById(int $id): Group
    {
        $stmt = $this->PDO->prepare('SELECT * FROM groups WHERE id = :id');
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result === false) {
            throw new \Exception('Group not found');
        }

        return Group::jsonDeserialize($result);
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->PDO->prepare('SELECT * FROM groups WHERE creator_id = :userId');
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $groups = [];
        foreach ($result as $row) {
            $groups[] = Group::jsonDeserialize($row);
        }

        return $groups;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Group;

use App\Domain\Objects\Group\Group;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\Objects\Message\Message;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Slim\Logger;

class InMemoryGroupMembersRepository extends Repository implements GroupMemberRepository
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
            'group_id' => 'INT',
            'user_id' => 'INT',
            'role' => 'TEXT',
        ];
    }

    public function getTableName(): string
    {
        return 'group_members';
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

    public function findGroupMembers(int $groupId): array
    {
        $stmt = $this->PDO->prepare('SELECT * FROM group_members WHERE group_id = :groupId');
        $stmt->bindParam(':groupId', $groupId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $groupMembers = [];
        foreach ($result as $row) {
            $groupMembers[] = (new InMemoryUserRepository())->findUserOfId($row['user_id']);
        }

        return $groupMembers;
    }

    public function findUserGroups(int $userId): array
    {
        $stmt = $this->PDO->prepare('SELECT * FROM group_members WHERE user_id = :userId');
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

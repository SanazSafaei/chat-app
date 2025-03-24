<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Group;

use App\Domain\Objects\DomainObject;
use App\Domain\Objects\Group\Group;
use App\Domain\Objects\Group\GroupMember;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;

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

    public function findGroupMembers(int $groupId): array
    {
        $stmt = $this->PDO->prepare('SELECT * FROM group_members WHERE group_id = :groupId');
        $stmt->bindParam(':groupId', $groupId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $groupMembers = [];
        foreach ($result as $row) {
            $userData = (new InMemoryUserRepository())->findUserOfId($row['user_id']);
            $groupMember = GroupMember::jsonDeserialize($row);
            $groupMember->setUserData($userData);
            $groupMembers[] = $groupMember;
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

    public function getByUserIdAndGroupId(int $userId, int $groupId): ?DomainObject
    {
        $stmt = $this->PDO->prepare('SELECT * FROM group_members WHERE user_id = :userId AND group_id = :groupId');
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->bindParam(':groupId', $groupId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result === false) {
            return null;
        }

        return GroupMember::jsonDeserialize($result);
    }

    public function deleteByUserIdAndGroupId(int $userId, int $groupId): void
    {
        $stmt = $this->PDO->prepare('DELETE FROM group_members WHERE user_id = :userId AND group_id = :groupId');
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->bindParam(':groupId', $groupId, \PDO::PARAM_INT);
        $stmt->execute();
    }
}

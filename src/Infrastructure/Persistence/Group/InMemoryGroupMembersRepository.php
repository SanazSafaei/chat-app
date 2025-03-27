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
use PDO;

class InMemoryGroupMembersRepository extends Repository implements GroupMemberRepository
{
    public const string CACHE_GROUP_ID = 'group_members_';
    public const string CACHE_USER_ID = 'user_groups_';

    public function __construct(DBInterface $DB,)
    {
        parent::__construct($DB);
        $this->createTable();
    }

    public function getFields(): array
    {
        return [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'group_id' => 'INTEGER',
            'user_id' => 'INTEGER',
            'role' => 'TEXT',
        ];
    }

    public function getTableName(): string
    {
        return 'group_members';
    }

    public function getCacheKeys(): array
    {
        return [
            'group_id' => self::CACHE_GROUP_ID,
            'user_id' => self::CACHE_USER_ID
        ];
    }

    public function findGroupMembers(int $groupId): array
    {
        $cacheKey = self::CACHE_GROUP_ID . $groupId;
        $groupMembersCacheItem = $this->cache->getItem($cacheKey);

        if (!$groupMembersCacheItem->isHit()) {
            $stmt = $this->PDO->prepare('SELECT * FROM group_members WHERE group_id = :groupId');
            $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();

            $groupMembers = [];
            foreach ($result as $row) {
                $userData = (new InMemoryUserRepository($this->db))->findUserOfId($row['user_id']);
                /** @var GroupMember $groupMember */
                $groupMember = GroupMember::jsonDeserialize($row);
                $groupMember->setUserData($userData);
                $groupMembers[] = $groupMember;
            }

            $groupMembersCacheItem->set($groupMembers)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($groupMembersCacheItem);
            $this->logger->info("Group members for group ID `{$groupId}` were fetched from the database and cached.");
        } else {
            $groupMembers = $groupMembersCacheItem->get();
            $this->logger->info("Group members for group ID `{$groupId}` were fetched from the cache.");
        }

        return $groupMembers;
    }

    public function findUserGroups(int $userId): array
    {
        $cacheKey = self::CACHE_USER_ID . $userId;
        $userGroupsCacheItem = $this->cache->getItem($cacheKey);

        if (!$userGroupsCacheItem->isHit()) {
            $stmt = $this->PDO->prepare('SELECT * FROM group_members WHERE user_id = :userId');
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();

            $groups = [];
            foreach ($result as $row) {
                $groups[] = Group::jsonDeserialize($row);
            }

            $userGroupsCacheItem->set($groups)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($userGroupsCacheItem);
            $this->logger->info("User groups for user ID `{$userId}` were fetched from the database and cached.");
        } else {
            $groups = $userGroupsCacheItem->get();
            $this->logger->info("User groups for user ID `{$userId}` were fetched from the cache.");
        }

        return $groups;
    }

    public function getByUserIdAndGroupId(int $userId, int $groupId): ?DomainObject
    {
        $stmt = $this->PDO->prepare('SELECT * FROM group_members WHERE user_id = :userId AND group_id = :groupId');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
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
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->execute();

        // Invalidate the cache
        try {
            $this->cache->deleteItem(self::CACHE_GROUP_ID . $groupId);
        } catch (\InvalidArgumentException $exception) {}
        try {
            $this->cache->deleteItem(self::CACHE_USER_ID.$userId);
        } catch (\InvalidArgumentException $exception) {}
        $this->logger->info("Group member with user ID `{$userId}` and group ID `{$groupId}` was deleted and cache invalidated.");
    }
}
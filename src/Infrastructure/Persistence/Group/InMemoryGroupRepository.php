<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Group;

use App\Domain\Objects\Group\Group;
use App\Domain\Objects\Group\GroupRepository;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Log\LoggerInterface;
use DI\NotFoundException;
use PDO;

class InMemoryGroupRepository extends Repository implements GroupRepository
{
    const string CACHE_ID = 'group_';
    const string CACHE_USER = 'group_user_';
    public function __construct(DBInterface $DB)
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
            'description' => 'TEXT',
            'created_by' => 'INTEGER',
            'created_at' => 'TEXT',
            'updated_at' => 'TEXT'
        ];
    }

    public function getTableName(): string
    {
        return 'groups';
    }

    public function getCacheKeys(): array
    {
        return [
            'id' => self::CACHE_ID,
            'user_id' => self::CACHE_USER
        ];
    }

    public function findAll(): array
    {
        $result = $this->PDO->query('SELECT * FROM groups');
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Group::jsonDeserialize($row);
        }
        return $dtoArray;
    }

    public function findById(int $id): Group
    {
        $cacheKey = self::CACHE_ID . $id;
        $groupCacheItem = $this->cache->getItem($cacheKey);

        if (!$groupCacheItem->isHit()) {
            $stmt = $this->PDO->prepare('SELECT * FROM groups WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result === false) {
                throw new NotFoundException('Group not found');
            }

            $group = Group::jsonDeserialize($result);
            $groupCacheItem->set($group)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($groupCacheItem);
            $this->logger->info("Group with ID `{$id}` was fetched from the database and cached.");
        } else {
            $group = $groupCacheItem->get();
            $this->logger->info("Group with ID `{$id}` was fetched from the cache.");
        }

        return $group;
    }

    public function findByUserId(int $userId): array
    {
        $cacheKey = self::CACHE_USER . $userId;
        $groupCacheItem = $this->cache->getItem($cacheKey);

        if (!$groupCacheItem->isHit()) {
            $stmt = $this->PDO->prepare('SELECT * FROM groups WHERE creator_id = :userId');
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();

            $groups = [];
            foreach ($result as $row) {
                $groups[] = Group::jsonDeserialize($row);
            }
            $groupCacheItem->set($groups)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($groupCacheItem);
        } else {
            $groups = $groupCacheItem->get();
        }

        return $groups;
    }
}
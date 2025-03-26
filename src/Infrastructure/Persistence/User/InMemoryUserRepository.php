<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\Objects\User\User;
use App\Domain\Objects\User\UserNotFoundException;
use App\Domain\Objects\User\UserRepository;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class InMemoryUserRepository extends Repository implements UserRepository
{
    const string CACHE_ID = 'user_';
    const string CACHE_USERNAME = 'user_username_';
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
            'last_seen' => 'TEXT',
            'created_at' => 'TEXT',
            'updated_at' => 'TEXT'
        ];
    }

    public function getTableName(): string
    {
        return 'users';
    }

    public function getCacheKeys(): array
    {
        return [
            'id' => self::CACHE_ID,
            'username' => self::CACHE_USERNAME
        ];
    }

    public function findAll(): array
    {
        $result = $this->PDO->query('SELECT * FROM users');
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = User::jsonDeserialize($row);
        }
        return $dtoArray;
    }

    public function findUserOfId(int $id): User
    {
        $cacheKey = self::CACHE_ID . $id;
        $userCacheItem = $this->cache->getItem($cacheKey);

        if (!$userCacheItem->isHit()) {
            $result = $this->PDO->query("SELECT * FROM users WHERE id = $id");
            $result = $result->fetch();
            if (!isset($result) || !$result) {
                throw new UserNotFoundException();
            }

            $user = User::jsonDeserialize($result);
            $userCacheItem->set($user)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($userCacheItem);
        } else {
            $user = $userCacheItem->get();
            $this->logger->info("User with ID `{$id}` was fetched from the cache.");
        }

        return $user;
    }

    public function findUserOfUsername(string $username): User
    {
        $cacheKey = self::CACHE_USERNAME . $username;
        $userCacheItem = $this->cache->getItem($cacheKey);

        if (!$userCacheItem->isHit()) {
            $stmt = $this->PDO->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $result = $stmt->fetch();

            if (!isset($result) || !$result) {
                throw new UserNotFoundException();
            }

            $user = User::jsonDeserialize($result);
            $userCacheItem->set($user)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($userCacheItem);
        } else {
            $user = $userCacheItem->get();
            $this->logger->info("User with ID `{$user->getId()}` was fetched from the cache.");
        }
        return $user;
    }
}
<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Media;

use App\Domain\Objects\Media\Media;
use App\Domain\Objects\Media\MediaRepository;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;
use DI\NotFoundException;

class InMemoryMediaRepository extends Repository implements MediaRepository
{
    public const string CACHE_ID = 'media_';
    public const int CACHE_TTL = 60 * 60; // 1 hour cache

    public function __construct(DBInterface $DB)
    {
        parent::__construct($DB);
        $this->createTable();
    }

    public function getFields(): array
    {
        return [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'file_name' => 'TEXT',
            'file_type' => 'TEXT',
            'path' => 'TEXT'
        ];
    }

    public function getTableName(): string
    {
        return 'medias';
    }

    public function getCacheKeys(): array
    {
        return [
            'id' => self::CACHE_ID
        ];
    }

    public function findAll(): array
    {
        $result = $this->PDO->query('SELECT * FROM medias');
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Media::jsonDeserialize($row);
        }
        return $dtoArray;
    }

    public function findById(int $id): Media
    {
        $cacheKey = self::CACHE_ID . $id;
        $mediaCacheItem = $this->cache->getItem($cacheKey);

        if (!$mediaCacheItem->isHit()) {
            $result = $this->PDO->query("SELECT * FROM medias WHERE id = $id");
            $result = $result->fetch();
            if (!isset($result) || !$result) {
                throw new NotFoundException();
            }

            $media = Media::jsonDeserialize($result);
            $mediaCacheItem->set($media)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($mediaCacheItem);
            $this->logger->info("Media with ID `{$id}` was fetched from the database and cached.");
        } else {
            $media = $mediaCacheItem->get();
            $this->logger->info("Media with ID `{$id}` was fetched from the cache.");
        }

        return $media;
    }
}
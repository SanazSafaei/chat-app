<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Media;

use App\Domain\Objects\Media\Media;
use App\Domain\Objects\Media\MediaRepository;
use App\Domain\Objects\User\User;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;
use DI\NotFoundException;

/** this class should change to proper object storage */
class InMemoryMediaRepository extends Repository implements MediaRepository
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
            'file_name' => 'TEXT',
            'file_type' => 'TEXT',
            'path' => 'TEXT'
        ];
    }

    public function getTableName(): string
    {
        return 'medias';
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
        $result = $this->PDO->query("SELECT * FROM medias WHERE id = $id");
        $result = $result->fetch();
        if (!isset($result) || !$result) {
            throw new NotFoundException();
        }

        return Media::jsonDeserialize($result);
    }
}

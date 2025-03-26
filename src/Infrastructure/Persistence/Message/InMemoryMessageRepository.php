<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Message;

use App\Domain\Objects\Message\Message;
use App\Domain\Objects\Message\MessageRepository;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;
use Slim\Logger;

class InMemoryMessageRepository extends Repository implements MessageRepository
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
            'from_id' => 'INTEGER',
            'to_id' => 'INTEGER',
            'type' => "TEXT",
            'message' => 'TEXT',
            'media' => 'TEXT',
            'created_at' => 'TEXT'
        ];
    }

    public function getTableName(): string
    {
        return 'messages';
    }

    public function findAll(): array
    {
        $result = $this->PDO->query('SELECT * FROM messages');
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Message::jsonDeserialize($row);
        }
        return $dtoArray;
    }

    public function findMessagesFromToId(int $to, int $from, string $type): array
    {
        $result = $this->PDO->query(
            "SELECT * FROM messages WHERE (from_id = $from and to_id = $to)
                          OR (from_id = $to and to_id = $from) AND type = '$type'"
        );
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $logger = new Logger();
            $logger->log('info', '---------->' . $row['id']);
            $dtoArray[] = Message::jsonDeserialize($row);
        }

        return $dtoArray;
    }

    public function findMessagesToGroupId(int $to): array
    {
        $result = $this->PDO->query("SELECT * FROM messages WHERE to_id = $to and type = GroupRepository::TYPE_GROUP");
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Message::jsonDeserialize($row);
        }

        return $dtoArray;
    }

    public function findMessageOfMediaId(int $to, int $from, string $type, int $mediaId): array
    {
        $result = $this->PDO->query(
            "SELECT * FROM messages WHERE (from_id = $from and to_id = $to) 
                          OR (from_id = $to and to_id = $from) 
                                 AND type = '$type' 
                                 AND media = $mediaId"
        );
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Message::jsonDeserialize($row);
        }

        return $dtoArray;
    }

    public function findMessageWithUserIdAndMediaId(int $to, int $mediaId): array
    {
        $result = $this->PDO->query(
            "SELECT * FROM messages WHERE (from_id = $to or to_id = $to)  AND media = {$mediaId}"
        );
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $logger = new Logger();
            $logger->log('info', '---------->' . $row['id']);
            $dtoArray[] = Message::jsonDeserialize($row);
        }

        return $dtoArray;
    }
}

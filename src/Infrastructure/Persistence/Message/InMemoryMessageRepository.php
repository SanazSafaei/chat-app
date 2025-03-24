<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Message;

use App\Domain\Objects\Message\Message;
use App\Domain\Objects\Message\MessageRepository;
use App\Infrastructure\Persistence\DBInterface;
use App\Infrastructure\Persistence\Repository;

class InMemoryMessageRepository extends Repository implements MessageRepository
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
            'from_id' => 'INT',
            'to_id' => 'INT',
            'type' => "TEXT",
            'message' => 'TEXT',
            'media' => 'TEXT',
            'created_at' => 'datetime'
        ];
    }

    public function getTableName(): string
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $result = $this->PDO->query('SELECT * FROM messages');
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Message::jsonDeserialize($row);
        }
        return $dtoArray;
    }

    /**
     * {@inheritdoc}
     */
    public function findMessagesFromToId(int $to, int $from, ?string $type = null): array
    {
        $result = $this->PDO->query("SELECT * FROM messages WHERE (from_id = {$from} and to_id = {$to}) OR (from_id = {$to} and to_id = {$from}) AND type = '{$type}'");
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Message::jsonDeserialize($row);
        }

        return $dtoArray;
    }

    public function findMessagesToGroupId(int $to): array
    {
        $result = $this->PDO->query("SELECT * FROM messages WHERE to_id = {$to} and type = GroupRepository::TYPE_GROUP");
        $dtoArray = [];
        foreach ($result->fetchAll() as $row) {
            $dtoArray[] = Message::jsonDeserialize($row);
        }

        return $dtoArray;
    }

}

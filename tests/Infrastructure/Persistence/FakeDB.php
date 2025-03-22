<?php

namespace Tests\Infrastructure\Persistence;

use App\Infrastructure\Persistence\DBInterface;
use PDO;

class FakeDB implements DBInterface
{
    private PDO $connection;
    private string $path;

    public function __construct()
    {
        $this->path = __DIR__ . '/fake_db.sqlite';
        $this->connection = new PDO('sqlite:/' . $this->path);
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function deleteDB(): void
    {
        unlink($this->path);
    }
}

<?php

namespace App\Infrastructure\Persistence;

use PDO;

class DB implements DBInterface
{
    private string $path;
    private PDO $connection;

    public function __construct()
    {
        $this->path = 'sqlite:/' . __DIR__ . '/../../../var/database.sqlite';
        $this->connection = new PDO($this->path);
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}

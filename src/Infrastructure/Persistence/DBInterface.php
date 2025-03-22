<?php

namespace App\Infrastructure\Persistence;

use PDO;

interface DBInterface
{
    public function getConnection(): PDO;
}

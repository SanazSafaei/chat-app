<?php

namespace App\Infrastructure\Persistence;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Objects\DomainObject;
use DI\Attribute\Inject;
use Slim\Logger;
use Exception;
use PDO;

abstract class Repository
{
    protected PDO $PDO;
    protected Logger $logger;
    protected DBInterface $db;

    public const int CACHE_TTL = 5 * 60; // 5 minute cache
    protected FilesystemAdapter $cache;

    #[Inject(['DB' => DBInterface::class])]
    public function __construct(DBInterface $DB)
    {
        $this->db = $DB;
        $this->logger = new Logger();
        $this->PDO = ($this->getDB($DB))->getConnection();
        $this->cache = new FilesystemAdapter();
    }

    abstract public function getTableName(): string;
    abstract public function getFields(): array;

    public function getCacheKeys(): array
    {
        //map of $field => $key
        return [];
    }

    public function getCreateTableSchema(): string
    {
        // 'CREATE TABLE IF NOT EXISTS users
        // (id INTEGER PRIMARY KEY, username TEXT, first_name TEXT, last_name TEXT, photo TEXT, last_seen datetime,
        // created_at datetime, updated_at datetime)'
        $fields = $this->getFields();
        $tableDefinition = '(';
        foreach ($fields as $fieldName => $fieldType) {
            $tableDefinition = $tableDefinition . ' ' . $fieldName . ' ' . $fieldType . ',';
        }
        $tableDefinition = rtrim($tableDefinition, ",") . ' )';

        return 'CREATE TABLE IF NOT EXISTS ' . $this->getTableName() . ' ' . $tableDefinition;
    }

    public function createTable(): void
    {
        $this->PDO->query($this->getCreateTableSchema());
    }

    public function insert(DomainObject $domain): DomainObject
    {
        //$this->myPDO->query('INSERT INTO users
        // (id, username, first_name, last_name, photo, last_seen, created_at, updated_at)
        //VALUES (2, "bill.gates", "Bill", "Gates", "test/photo", DateTime(), DateTime(), DateTime())')->execute();

        $query = 'INSERT INTO ' . $this->getTableName() . ' (';
        foreach ($this->getFields() as $fieldName => $fieldType) {
            if ($this->shouldIgnoreField($fieldName, $fieldType)) {
                continue;
            }
            $query = $query . ' ' . $fieldName . ',';
        }
        $query = rtrim($query, ",") . ' )';

        $query = $query . ' VALUES (';
        $values = $domain->jsonSerialize();
        foreach ($this->getFields() as $fieldName => $fieldType) {
            if ($this->shouldIgnoreField($fieldName, $fieldType)) {
                continue;
            }
            if ($fieldType == 'TEXT') {
                $query = $query . ' \'' . $values[$fieldName] . '\',';
            } else {
                $query = $query . ' ' . $values[$fieldName] . ' ,';
            }
        }
        $this->logger->log('info', $query);
        $query = rtrim($query, ",") . ' )';
        $this->logger->log('info', $query);

        $result = $this->PDO->prepare($query);
        $execResult = $result->execute();
        if (!$execResult) {
            $error = $this->PDO->errorInfo();
            $this->logger->log('info', "Error: (" . $error[0] . ':' . $error[1] . ') ' . $error[2]);
            throw new Exception('DB is not available');
        }
        $domain->setId($this->PDO->lastInsertId());
        return $domain;
    }

    public function updateField($field, $value, $id): void
    {
        $query = "UPDATE users SET $field = :value WHERE id = :id";
        $stmt = $this->PDO->prepare($query);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception('DB is not available');
        }
        $this->resetCacheKeys($field, $value);
    }

    public function resetCacheKeys($field, $value): void
    {
        $cache = new FilesystemAdapter();
        $cacheKeys = $this->getCacheKeys();
        if (isset($cacheKeys[$field])) {
            $fullNameCacheKey = $cacheKeys[$field] . $value;
            try {
                $cache->deleteItem($fullNameCacheKey);
                $cache->delete($fullNameCacheKey);
                $this->logger->log('info', "Cache of $field with $value deleted.");
            } catch (InvalidArgumentException $exception) {
            }
        }
    }

    public function shouldIgnoreField(int|string $fieldName, mixed $fieldType): bool
    {
        return $fieldName == 'id' && $fieldType == 'INTEGER PRIMARY KEY AUTOINCREMENT';
    }

    private function getDB(?DBInterface $DB = null): DBInterface
    {
        return $DB ?: new DB();
    }
}

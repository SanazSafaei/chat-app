<?php

namespace App\Infrastructure\Persistence;

use App\Domain\DomainObject;

abstract class Repository
{
    protected \PDO $PDO;

    public function __construct()
    {
        $this->PDO = (new DB())->getConnection();
    }

    public abstract function getTableName(): string;
    public abstract function getFields(): array;

    public function getCreateTableSchema(): string
    {
        // 'CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT, first_name TEXT, last_name TEXT, photo TEXT, last_seen datetime, created_at datetime, updated_at datetime)'
        $fields = $this->getFields();
        $tableDefinition = '(';
        foreach ($fields as $fieldName => $fieldType) {
            $tableDefinition = $tableDefinition . ' '. $fieldName . ' ' . $fieldType . ',';
        }
        $tableDefinition = rtrim($tableDefinition, ",") . ' )';

        return 'CREATE TABLE IF NOT EXISTS ' . $this->getTableName() . ' '. $tableDefinition;
    }

    public function createTable(): void
    {
        $logger = new \Slim\Logger();
        $logger->log('info', $this->getCreateTableSchema());
        $this->PDO->query(
            $this->getCreateTableSchema()
        )->execute();
    }

    public function insert(DomainObject $domain): void
    {
        //$this->myPDO->query('INSERT INTO users (id, username, first_name, last_name, photo, last_seen, created_at, updated_at) VALUES (2, "bill.gates", "Bill", "Gates", "test/photo", DateTime(), DateTime(), DateTime())')->execute();
        $logger = new \Slim\Logger();

        $query = 'INSERT INTO '. $this->getTableName() . ' (';
        foreach ($this->getFields() as $fieldName => $fieldType) {
            if ($this->shouldIgnoreField($fieldName, $fieldType)) {
                continue;
            }
            $query = $query .' '. $fieldName . ',';
        }
        $query = rtrim($query, ",") . ' )';

        $query = $query . ' VALUES (';
        $values = $domain->jsonSerialize();
        foreach ($this->getFields() as $fieldName => $fieldType) {
            if ($this->shouldIgnoreField($fieldName, $fieldType)) {
                continue;
            }
            $query = $query . ' \'' . $values[$fieldName] . '\',';
        }
        $query = rtrim($query, ",") . ' )';

        $logger->log('error', $query);
        $this->PDO->query($query)->execute();
    }

    /**
     * @param int|string $fieldName
     * @param mixed $fieldType
     * @return bool
     */
    public function shouldIgnoreField(int|string $fieldName, mixed $fieldType): bool
    {
        return $fieldName == 'id' && $fieldType == 'INTEGER PRIMARY KEY AUTOINCREMENT';
    }

}
<?php

namespace CWSpear\SchemaDefinition;

use CWSpear\SchemaDefinition\Db\AdapterInterface;
use CWSpear\SchemaDefinition\Differ\DifferInterface;
use CWSpear\SchemaDefinition\Filesystem\FilesystemInterface;

class Manager
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter, FilesystemInterface $file)
    {
        $this->adapter   = $adapter;
        $this->file      = $file;
    }

//    public static function fromConfig(array $config)
//    {
//        $adapter = new Adapter($config['username'], $config['password'], $config['database']);
//        $file    = new Filesystem($config['schemas'], $config['migrations']);
//        return new static($adapter, $file);
//    }

    public function getTables()
    {
        return $this->adapter->getTables();
    }

    public function getFields($table)
    {
        return $this->adapter->getFields($table);
    }

    public function getIndexes($table)
    {
        return $this->adapter->getIndexes($table);
    }

    public function getForeignKeys($table)
    {
        return $this->adapter->getForeignKeys($table);
    }

    public function generateSchema($table)
    {
        $schema = [
            'fields'      => $this->getFields($table),
            'foreignKeys' => $this->getForeignKeys($table),
            'indexes'     => $this->getIndexes($table),
        ];

        return $schema;
    }

    public function createMigration($table, DifferInterface $differ)
    {
        return $this->file->saveMigration($table, $differ);
    }

    public function saveSchema($table, $schema)
    {
        return $this->file->saveSchema($table, $schema);
    }
}

<?php namespace CWSpear\SchemaDefinition\Db;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;

class Adapter implements AdapterInterface
{
    /**
     * @var \Doctrine\DBAL\Connection;
     */
    public $conn;

    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager;
     */
    public $schema;

    /**
     * {@inheritdoc}
     */
    public function __construct($host, $username, $password, $database, $driver)
    {
        $config = new Configuration;

        $connectionParams = [
            'host'     => $host,
            'user'     => $username,
            'password' => $password,
            'dbname'   => $database,
            'driver'   => $driver,
        ];

        $this->conn = DriverManager::getConnection($connectionParams, $config);

        $this->schema = $this->conn->getSchemaManager();
    }

    /**
     * {@inheritdoc}
     */
    public function getTables()
    {
        return $this->schema->listTableNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns($tableName)
    {
        $cols = array_map(function (Column $column) {
            $col = [
                'name'      => $column->getName(),
                'type'      => $column->getType()->getName(),
                'identity'  => $column->getAutoincrement() ?: null,
                'nullable'  => !$column->getNotnull() ?: null,
                'unsigned'  => $column->getUnsigned() ?: null,
                'length'    => $column->getLength(),
                'scale'     => $column->getScale(),
                'precision' => $column->getPrecision(),
                'default'   => $column->getDefault(),
                'comment'   => $column->getComment(),
                'update'    => null, // hmmm
                'after'     => null, // irrelevant for export
            ];

            // dbal has defaults for precision and scale for EVERY type...
            if (!in_array($col['type'], ['float', 'decimal'])) {
                unset($col['precision'], $col['scale']);
            }

            // remove null values as they are "defaults"
            return array_filter($col, function ($item) {
                return !is_null($item);
            });
        }, $this->schema->listTableColumns($tableName));

        $columns = [];

        foreach ($cols as $col) {
            $name = $col['name'];
            unset($col['name']);
            $columns[$name] = $col;
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexes($table)
    {
        $rows = $this->schema->listTableIndexes($table);

        return array_map(function (Index $index) {
            $row = ['columns' => $index->getColumns()];
            if ($index->isPrimary()) {
                $row['primary'] = true;
            }
            if ($index->isUnique()) {
                $row['unique'] = true;
            }

            return $row;
        }, $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function getForeignKeys($table)
    {
        $rows = $this->schema->listTableForeignKeys($table);

        return array_reduce($rows, function ($result, ForeignKeyConstraint $row) {
            $result[$row->getName()] = [
                'columns'         => $row->getColumns(),
                'foreign_table'   => $row->getForeignTableName(),
                'foreign_columns' => $row->getForeignColumns(),
            ];
            return $result;
        }, []);
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($column, $table)
    {
        $table = $this->schema->listTableDetails($table);

        return $table->hasColumn($column);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTable($table)
    {
        return $this->schema->tablesExist($table);
    }
}

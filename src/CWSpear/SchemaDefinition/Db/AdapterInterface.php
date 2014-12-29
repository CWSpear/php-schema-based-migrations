<?php namespace CWSpear\SchemaDefinition\Db;

interface AdapterInterface
{
    /**
     * Create an adapter with connection info.
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($host, $username, $password, $database);

    /**
     * Gets a list of the names of the tables in the configured DB.
     *
     * @return array
     */
    public function getTables();

    /**
     * Get a list of fields and properties from a table.
     *
     * @param string $table
     * @return array
     */
    public function getFields($table);

    /**
     * Get a list of indexes from a table;
     *
     * @param string $table
     * @return array
     */
    public function getIndexes($table);

    /**
     * Get a list of foreign keys from a table;
     *
     * @param string $table
     * @return array
     */
    public function getForeignKeys($table);

    /**
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function hasColumn($column, $table);

    /**
     * @param string $table
     * @return boolean
     */
    public function hasTable($table);
}
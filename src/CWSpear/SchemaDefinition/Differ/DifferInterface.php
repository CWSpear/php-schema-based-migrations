<?php namespace CWSpear\SchemaDefinition\Differ;


use Exception;

interface DifferInterface
{
    const DROP_TABLE = 100;

    const FIELDS = 'fields';

    /**
     * @param array $db   The schema as it exists in the DB
     * @param array $file The schema as it exists in the files
     */
    public function __construct(array $db, array $file);

    /**
     * Get fields et all that need to be added
     *
     * @return array
     */
    public function getAdded();

    /**
     * Get fields et all that need to be removed
     *
     * @return array
     */
    public function getRemoved();


    /**
     * Get fields et all that need to be altered on the way up
     *
     * @return array
     */
    public function getAlteredUp();

    /**
     * Get fields et all that need to be altered on the way down
     *
     * @return array
     */
    public function getAlteredDown();

    /**
     * Generated a diff from $origin
     *
     * @param array $origin
     * @param array $destination
     * @return array
     */
    public function diff(array $origin, array $destination);
}
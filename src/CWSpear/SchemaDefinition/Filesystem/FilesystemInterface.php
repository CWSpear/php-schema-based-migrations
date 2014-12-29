<?php namespace CWSpear\SchemaDefinition\Filesystem;

use CWSpear\SchemaDefinition\Differ\DifferInterface;
use CWSpear\SchemaDefinition\Generator\GeneratorInterface;
use CWSpear\SchemaDefinition\Parser\ParserInterface;

interface FilesystemInterface
{
    /**
     * Initialize Filesystem with knowledge about the paths it needs
     * to know to save files and the format in which it should read/write
     *
     * @param ParserInterface    $parser
     * @param GeneratorInterface $generator
     * @param string             $schemaPath
     * @param string             $migrationPath
     */
    public function __construct(ParserInterface $parser, GeneratorInterface $generator, $schemaPath, $migrationPath);

    /**
     * Save the schema to the schema directory
     *
     * @param string $table
     * @param array  $schema
     * @return bool  Whether or not the save was successful
     */
    public function saveSchema($table, array $schema);

    /**
     * Save the changes to the migration directory
     *
     * @param string          $table
     * @param DifferInterface $differ
     * @return bool Whether or not the save was successful
     */
    public function saveMigration($table, DifferInterface $differ);

    /**
     * Read the schema and parse it into an array
     *
     * @param string $table
     * @return array
     */
    public function loadSchema($table);

    /**
     * Wrapper for file_get_contents
     *
     * @param $filename
     * @return string
     */
    public function getFileContents($filename);
}
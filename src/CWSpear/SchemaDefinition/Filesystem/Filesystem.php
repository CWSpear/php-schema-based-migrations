<?php namespace CWSpear\SchemaDefinition\Filesystem;

use CWSpear\SchemaDefinition\Differ\DifferInterface;
use CWSpear\SchemaDefinition\Generator\GeneratorInterface;
use CWSpear\SchemaDefinition\Parser\ParserInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem implements FilesystemInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var SymfonyFilesystem
     */
    public $filesystem;

    /**
     * @var string
     */
    protected $schemaPath;

    /**
     * @var string
     */
    protected $migrationPath;

    /**
     * {@inheritdoc}
     */
    public function __construct(ParserInterface $parser, GeneratorInterface $generator, $schemaPath, $migrationPath)
    {
        $this->parser        = $parser;
        $this->generator     = $generator;
        $this->filesystem    = new SymfonyFilesystem;
        $this->schemaPath    = rtrim($schemaPath, '/');
        $this->migrationPath = rtrim($migrationPath, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function saveSchema($table, array $schema)
    {
        $contents = $this->parser->serialize($schema);

        $this->filesystem->dumpFile("{$this->schemaPath}/{$table}.{$this->parser->getExt()}", $contents);
    }

    /**
     * {@inheritdoc}
     */
    public function saveMigration($table, DifferInterface $differ)
    {
        $contents = $this->generator->generateMigration($table, $differ);

        $generatedName = $this->generator->generateName();

        $this->filesystem->dumpFile("{$this->migrationPath}/{$generatedName}.php", $contents);
    }

    /**
     * Read the schema and parse it into an array
     *
     * @param string $table
     * @return array
     */
    public function loadSchema($table)
    {
        $contents = $this->getFileContents("{$this->schemaPath}/{$table}.{$this->parser->getExt()}");

        return $this->parser->parse($contents);
    }

    public function getFileContents($filename)
    {
        return file_get_contents($filename);
    }
}

<?php

namespace CWSpear\SchemaDefinition;

use CWSpear\SchemaDefinition\Db\AdapterInterface;
use CWSpear\SchemaDefinition\Db\Adapter;
use CWSpear\SchemaDefinition\Differ\DifferInterface;
use CWSpear\SchemaDefinition\Exception\FileNotFoundException;
use CWSpear\SchemaDefinition\Exception\InvalidConfigException;
use CWSpear\SchemaDefinition\Exception\UnsupportedFormatException;
use CWSpear\SchemaDefinition\Exception\UnsupportedGeneratorException;
use CWSpear\SchemaDefinition\Filesystem\Filesystem;
use CWSpear\SchemaDefinition\Filesystem\FilesystemInterface;
use CWSpear\SchemaDefinition\Generator\LaravelGenerator;
use CWSpear\SchemaDefinition\Parser\JsonParser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Yaml\Yaml;

class Manager
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter, FilesystemInterface $file)
    {
        $this->adapter = $adapter;
        $this->file    = $file;
    }

    public static function fromConfig(array $config)
    {
        self::assertValidConfig($config);

        switch (strtolower($config['format'])) {
            case 'json':
                $parser = new JsonParser;
                break;

            default:
                throw new UnsupportedFormatException("\"{$config['format']}\" format is not (yet?) supported");
        }

        switch (strtolower($config['generator'])) {
            case 'laravel':
                $generator = new LaravelGenerator;
                break;

            default:
                throw new UnsupportedGeneratorException("\"{$config['generator']}\" generator is not (yet?) supported");
        }

        $adapter = new Adapter($config['host'], $config['username'], $config['password'], $config['database'], $config['adapter']);
        $file    = new Filesystem($parser, $generator, $config['schemas'], $config['migrations']);

        return new static($adapter, $file);
    }

    public static function fromInput(InputInterface $input, YamlParser $yaml = null)
    {
        $configPath = $input->getOption('config');
        $contents   = @file_get_contents($configPath);

        if ($contents === false) {
            throw new FileNotFoundException("Config file \"{$configPath}\" not found.");
        }

        $config = is_null($yaml) ? Yaml::parse($contents) : $yaml->parse($contents);

        return Manager::fromConfig($config);
    }

    public static function assertValidConfig($config)
    {
        $errors = [];

        $requiredFields = [
            'adapter',
            'host',
            'username',
            'password',
            'database',
            'format',
            'schemas',
            'migrations',
            'generator',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($config[$field])) {
                $errors[] = "Missing required option: \"{$field}\".";
            }
        }

        if (!empty($errors)) {
            throw new InvalidConfigException(implode("\n ", $errors));
        }
    }

    public static function splitTableList($tableStr)
    {
        if (is_null($tableStr)) {
            return null;
        }

        return preg_split('/ *, */', $tableStr);
    }

    public function getTables()
    {
        return $this->adapter->getTables();
    }

    public function getFields($table)
    {
        return $this->adapter->getColumns($table);
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

    public function export(array $tables = null, OutputInterface $output = null)
    {
        if (is_null($tables)) {
            $tables = $this->getTables();
        }

        foreach ($tables as $table) {
            if (!is_null($output)) {
                $output->writeln("[<comment>{$table}</comment>] starting export");
            }

            $this->saveSchema($table, $this->generateSchema($table));

            if (!is_null($output)) {
                $output->writeln("[<comment>{$table}</comment>] successfully exported\n");
            }
        }
    }
}

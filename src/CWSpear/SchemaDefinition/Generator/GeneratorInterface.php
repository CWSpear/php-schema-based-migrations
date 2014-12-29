<?php namespace CWSpear\SchemaDefinition\Generator;

use CWSpear\SchemaDefinition\Differ\DifferInterface;

interface GeneratorInterface
{
    public function generateMigration($table, DifferInterface $differ);

    public function generateName();
}
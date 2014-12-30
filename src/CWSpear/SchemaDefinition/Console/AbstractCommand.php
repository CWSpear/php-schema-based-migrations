<?php namespace CWSpear\SchemaDefinition\Console;

use CWSpear\SchemaDefinition\Exception\FileNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

class AbstractCommand extends Command
{
    public function loadConfig(InputInterface $input)
    {
        $configPath = $input->getOption('config');
        $contents   = @file_get_contents($configPath);

        if ($contents === false) {
            throw new FileNotFoundException("Config file \"{$configPath}\" not found.");
        }

        return Yaml::parse($contents);
    }
}
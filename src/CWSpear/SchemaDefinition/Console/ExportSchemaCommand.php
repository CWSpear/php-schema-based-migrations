<?php namespace CWSpear\SchemaDefinition\Console;

use CWSpear\SchemaDefinition\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportSchemaCommand extends Command
{
    public function configure()
    {
        $this->setName('export')
             ->setDescription('Create schema files based on the current state of the database.')
             ->addOption('--config', '-c', InputOption::VALUE_OPTIONAL, 'Path to the config file.', 'schema.yml')
             ->addOption('--tables', '-t', InputOption::VALUE_OPTIONAL, 'Tables to export. If omitted, exports all tables.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = Manager::fromInput($input);

        $manager->export(Manager::splitTableList($input->getOption('tables')), $output);
    }
}

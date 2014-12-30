<?php namespace spec\CWSpear\SchemaDefinition;

use CWSpear\SchemaDefinition\Db\AdapterInterface;
use CWSpear\SchemaDefinition\Differ\DifferInterface;
use CWSpear\SchemaDefinition\Filesystem\FilesystemInterface;
use CWSpear\SchemaDefinition\Manager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * @mixin Manager
 */
class ManagerSpec extends ObjectBehavior
{
    protected $baseConfig = [
        'adapter'    => 'mysql',
        'host'       => '127.0.0.1',
        'username'   => 'root',
        'password'   => 'root',
        'database'   => 'schema_test',
        'format'     => 'json',
        'schemas'    => './spec/fixtures/actual/schemas',
        'migrations' => './spec/fixtures/actual/migrations',
        'generator'  => 'laravel',
    ];

    function let(AdapterInterface $adapter, FilesystemInterface $file)
    {
        $this->beConstructedWith($adapter, $file);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CWSpear\SchemaDefinition\Manager');
    }

    function it_should_be_able_to_be_initalized_via_a_config()
    {
        $this::fromConfig($this->baseConfig)->shouldHaveType('CWSpear\SchemaDefinition\Manager');
    }

    function it_should_be_able_to_be_initalized_via_cli_interface(InputInterface $input, YamlParser $yaml)
    {
        $input->getOption('config')->shouldBeCalled()->willReturn(__FILE__);
        $yaml->parse(file_get_contents(__FILE__))->shouldBeCalled()->willReturn($this->baseConfig);
        $this::fromInput($input, $yaml)->shouldHaveType('CWSpear\SchemaDefinition\Manager');
    }

    function it_should_throw_if_config_file_is_not_found_when_initializing_from_input(InputInterface $input)
    {
        $this->shouldThrow('\CWSpear\SchemaDefinition\Exception\FileNotFoundException')->duringFromInput($input);
    }

    function it_should_split_a_table_list()
    {
        $this::splitTableList('one,two,three')->shouldReturn(['one', 'two', 'three']);
        $this::splitTableList('one,two, three')->shouldReturn(['one', 'two', 'three']);
        $this::splitTableList('one , two ,three')->shouldReturn(['one', 'two', 'three']);
        $this::splitTableList('one, two   ,  three')->shouldReturn(['one', 'two', 'three']);
    }

    function it_should_return_null_if_a_null_table_list_is_passed_in()
    {
        $this::splitTableList(null)->shouldReturn(null);
    }

    function it_should_throw_when_using_invalid_options()
    {
        $config = array_merge($this->baseConfig, ['adapter' => 'banana']);
        $this->shouldThrow('CWSpear\SchemaDefinition\Exception\UnsupportedAdapterException')->duringFromConfig($config);

        $config = array_merge($this->baseConfig, ['format' => 'banana']);
        $this->shouldThrow('CWSpear\SchemaDefinition\Exception\UnsupportedFormatException')->duringFromConfig($config);

        $config = array_merge($this->baseConfig, ['generator' => 'banana']);
        $this->shouldThrow('CWSpear\SchemaDefinition\Exception\UnsupportedGeneratorException')->duringFromConfig($config);
    }
    
    function it_should_throw_when_config_is_invalid()
    {
        $config = $this->baseConfig;
        unset($config['generator']);
        $this->shouldThrow(new \CWSpear\SchemaDefinition\Exception\InvalidConfigException('Missing required option: "generator".'))->duringAssertValidConfig($config);
    }

    function it_should_get_a_list_of_tables_in_the_database(AdapterInterface $adapter)
    {
        $tables = ['users', 'roles', 'accounts'];

        $adapter->getTables()->shouldBeCalled()->willReturn($tables);

        $this->getTables()->shouldReturn($tables);
    }

    function it_should_get_a_list_of_fields_with_types_in_a_table(AdapterInterface $adapter)
    {
        $fields = ['fields'];

        $adapter->getFields('users')->shouldBeCalled()->willReturn($fields);

        $this->getFields('users')->shouldReturn($fields);
    }

    function it_should_get_a_list_of_indexes_in_a_table(AdapterInterface $adapter)
    {
        $indexes = ['indexes'];

        $adapter->getIndexes('users')->shouldBeCalled()->willReturn($indexes);

        $this->getIndexes('users')->shouldReturn($indexes);
    }

    function it_should_get_a_list_of_foreign_keys_in_a_table(AdapterInterface $adapter)
    {
        $foreignKeys = ['foreignKeys'];

        $adapter->getForeignKeys('users')->shouldBeCalled()->willReturn($foreignKeys);

        $this->getForeignKeys('users')->shouldReturn($foreignKeys);
    }

    function it_should_generate_schema_from_existing_table(AdapterInterface $adapter)
    {
        $schema = [
            'fields'      => 'fields',
            'foreignKeys' => 'foreignKeys',
            'indexes'     => 'indexes',
        ];

        $adapter->getFields('table')->willReturn('fields');
        $adapter->getForeignKeys('table')->willReturn('foreignKeys');
        $adapter->getIndexes('table')->willReturn('indexes');

        $this->generateSchema('table')->shouldReturn($schema);
    }

    function it_should_save_a_generated_schema(FilesystemInterface $file)
    {
        $table  = 'banana';
        $schema = [
            'fields'      => 'fields',
            'foreignKeys' => 'foreignKeys',
            'indexes'     => 'indexes',
        ];

        $file->saveSchema($table, $schema)->shouldBeCalled()->willReturn(true);

        $this->saveSchema($table, $schema)->shouldReturn(true);
    }

    function it_should_save_a_generated_migration(FilesystemInterface $file, DifferInterface $differ)
    {
        $table = 'banana';

        $file->saveMigration($table, $differ)->shouldBeCalled()->willReturn(true);

        $this->createMigration($table, $differ)->shouldReturn(true);
    }

    function it_should_export_specific_schema_with_table_param(OutputInterface $output)
    {
        $output->writeln('[<comment>one</comment>] starting export')->shouldBeCalled();
        $output->writeln('[<comment>one</comment>] successfully exported' . "\n")->shouldBeCalled();
        $output->writeln('[<comment>two</comment>] starting export')->shouldNotBeCalled();
        $output->writeln('[<comment>two</comment>] successfully exported' . "\n")->shouldNotBeCalled();

        $this->export(['one'], $output);
    }

    function it_should_export_all_schema_with_no_table_param(AdapterInterface $adapter, OutputInterface $output)
    {
        $adapter->getTables()->shouldBeCalled()->willReturn(['one', 'two']);
        $adapter->getFields('one')->shouldBeCalled();
        $adapter->getFields('two')->shouldBeCalled();
        $adapter->getIndexes('one')->shouldBeCalled();
        $adapter->getIndexes('two')->shouldBeCalled();
        $adapter->getForeignKeys('one')->shouldBeCalled();
        $adapter->getForeignKeys('two')->shouldBeCalled();

        $output->writeln('[<comment>one</comment>] starting export')->shouldBeCalled();
        $output->writeln('[<comment>one</comment>] successfully exported' . "\n")->shouldBeCalled();
        $output->writeln('[<comment>two</comment>] starting export')->shouldBeCalled();
        $output->writeln('[<comment>two</comment>] successfully exported' . "\n")->shouldBeCalled();

        $this->export(null, $output);
    }
}

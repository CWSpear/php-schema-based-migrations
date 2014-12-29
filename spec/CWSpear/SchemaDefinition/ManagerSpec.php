<?php namespace spec\CWSpear\SchemaDefinition;

use CWSpear\SchemaDefinition\Db\AdapterInterface;
use CWSpear\SchemaDefinition\Differ\DifferInterface;
use CWSpear\SchemaDefinition\Filesystem\FilesystemInterface;
use CWSpear\SchemaDefinition\Manager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Manager
 */
class ManagerSpec extends ObjectBehavior
{
    function let(AdapterInterface $adapter, FilesystemInterface $file)
    {
        $this->beConstructedWith($adapter, $file);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CWSpear\SchemaDefinition\Manager');
    }

//    function it_should_be_able_to_be_initalized_via_a_config()
//    {
//        Manager::fromConfig([
//            'driver'     => 'mysql',
//            'host'       => ',',
//            'username'   => 'root',
//            'password'   => 'root',
//            'database'   => 'schema_test',
//            'format'     => 'json',
//            'schemas'    => 'spec/fixtures/actual/schemas',
//            'migrations' => 'spec/fixtures/actual/migrations',
//        ])->shouldHaveType('CWSpear\SchemaDefinition\Manager');
//    }

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
        $table = 'banana';
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
}

<?php namespace spec\CWSpear\SchemaDefinition\Db;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin MysqlAdapter
 */
class AdapterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('127.0.0.1', 'root', 'root', 'schema_test', 'pdo_mysql');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CWSpear\SchemaDefinition\Db\Adapter');
    }

    function it_should_get_a_list_of_tables()
    {
        $this->getTables()->shouldReturn(['diverse', 'second']);
    }

    function it_should_get_details_about_columns_in_a_table()
    {
        $this->getColumns('diverse')->shouldReturn([
            'id'                      => [
                'type'     => 'integer',
                'identity' => true,
                'unsigned' => true,
            ],
            'second_id'               => [
                'type'     => 'integer',
                'unsigned' => true,
            ],
            'signed_int_null_default' => [
                'type'     => 'integer',
                'nullable' => true,
                'default'  => '3',
            ],
            '100_char'                => [
                'type'   => 'string',
                'length' => 100,
            ],
            '200_char_null'           => [
                'type'     => 'string',
                'nullable' => true,
                'length'   => 200,
            ],
            'comment'                 => [
                'type'    => 'datetime',
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Time is fleeting',
            ],
            'float'                   => [
                'type'      => 'float',
                'scale'     => 10,
                'precision' => 12,
            ]
        ]);
    }

    function it_should_get_a_list_of_indexes()
    {
        $this->getIndexes('second')->shouldReturn([
            'primary' => [
                'columns' => ['id'],
                'primary' => true,
                'unique'  => true,
            ], 'id_two' => [
                'columns' => ['id_two'],
                'unique'  => true,
            ], 'id_three_id_four' => [
                'columns' => ['id_three', 'id_four'],
                'unique'  => true,
            ], 'id_five' => [
                'columns' => ['id_five'],
            ], 'id_six_id_seven' => [
                'columns' => ['id_six', 'id_seven'],
            ]
        ]);
    }

    function it_should_get_a_list_of_foreign_keys()
    {
        $this->getForeignKeys('diverse')->shouldReturn([
            'second_second_id_foreign' => [
                'columns'         => ['second_id'],
                'foreign_table'   => 'second',
                'foreign_columns' => ['id'],
            ]
        ]);
    }

    function it_should_determine_if_it_has_a_particular_table_or_not()
    {
        $this->hasColumn('banana', 'diverse')->shouldReturn(false);
        $this->hasColumn('id', 'diverse')->shouldReturn(true);
    }

    function it_should_determine_if_it_has_a_particular_column_or_not()
    {
        $this->hasTable('table')->shouldReturn(false);
        $this->hasTable('diverse')->shouldReturn(true);
    }
}

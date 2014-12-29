<?php namespace spec\CWSpear\SchemaDefinition\Db;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin MysqlAdapter
 */
class MysqlAdapterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('127.0.0.1', 'root', 'root', 'schema_test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CWSpear\SchemaDefinition\Db\MysqlAdapter');
    }

    function it_should_get_a_list_of_tables()
    {
        $this->getTables()->shouldReturn(['diverse', 'second']);
    }

    function it_should_get_details_about_columns_in_a_table()
    {
        $this->getFields('diverse')->shouldReturn([
            'id' => [
                'type'     => 'integer',
                'limit'    => 10,
                'identity' => true,
                'unsigned' => 'unsigned',
            ],
            'second_id' => [
                'type'     => 'integer',
                'limit'    => 10,
                'unsigned' => 'unsigned',
            ],
            'signed_int_null_default' => [
                'type'     => 'integer',
                'limit'    => 5,
                'default'  => '3',
                'nullable' => true,
            ],
            '100_char' => [
                'type'  => 'string',
                'limit' => 100,
            ],
            '200_char_null' => [
                'type'     => 'string',
                'limit'    => 200,
                'nullable' => true,
            ],
            'comment' => [
                'type'    => 'timestamp',
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Time is fleeting',
            ],
            'float' => [
                'type'      => 'float',
                'scale'     => 12,
                'precision' => 10,
            ]
        ]);
    }

    function it_should_get_a_list_of_indexes()
    {
        $this->getIndexes('second')->shouldReturn([
            [
                'columns' => ['id'],
                'unique' => true,
            ], [
                'columns' => ['id_two'],
                'unique' => true,
            ], [
                'columns' => ['id_three', 'id_four'],
                'unique' => true,
            ], [
                'columns' => ['id_five'],
                'unique' => false,
            ], [
                'columns' => ['id_six', 'id_seven'],
                'unique' => false,
            ]
        ]);
    }

    function it_should_get_a_list_of_foreign_keys()
    {
        $this->getForeignKeys('diverse')->shouldReturn([[
            'column'         => 'second_id',
            'foreign_table'  => 'diverse',
            'foreign_column' => 'id',
        ]]);
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

    function it_should_handle_bad_queries()
    {
        $this->shouldThrow('\PDOException')->duringQuery('BANANAS ARE GOOD');
    }

    function it_should_handle_bindings(\PDO $db, \PDOStatement $statement)
    {
        $sql = 'SELECT * FROM diverse WHERE id = ?';
        $bindings = [1];

        $this->db = $db;

        $db->prepare($sql)->shouldBeCalled()->willReturn($statement);
        $statement->execute($bindings)->shouldBeCalled()->willReturn($statement);
        $statement->fetchAll()->shouldBeCalled();

        $this->query($sql, $bindings);
    }
}

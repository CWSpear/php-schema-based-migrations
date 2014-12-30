<?php namespace spec\CWSpear\SchemaDefinition\Differ;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin DifferSpec
 */
class DifferSpec extends ObjectBehavior
{
    protected $fileTest;
    protected $dbTest;

    function let()
    {
        $this->dbTest = [
            'fields'      => [
                'id'                      => [
                    'type'     => 'integer',
                    'limit'    => 10,
                    'identity' => true,
                    'unsigned' => 'unsigned',
                ],
                'second_id'               => [
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
                '100_char'                => [
                    'type'  => 'string',
                    'limit' => 100,
                ],
                '200_char_null'           => [
                    'type'     => 'string',
                    'limit'    => 200,
                    'nullable' => true,
                ],
                'comment'                 => [
                    'type'    => 'timestamp',
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => 'Time is fleeting',
                ],
                'float'                   => [
                    'type'      => 'float',
                    'scale'     => 12,
                    'precision' => 10,
                ],
            ],
            'indexes'     => [],
            'foreignKeys' => [],
        ];

        $this->fileTest = [
            'fields'      => [
                'id'                      => [
                    'type'     => 'integer',
                    'limit'    => 10,
                    'identity' => true,
                    'unsigned' => 'unsigned',
                ],
                'the_real_id'             => [
                    'type'     => 'integer',
                    'limit'    => 10,
                    'unsigned' => 'unsigned',
                ],
                'signed_int_null_default' => [
                    'type'     => 'integer',
                    'limit'    => 5,
                    'nullable' => true,
                ],
                '200_char_null'           => [
                    'type'     => 'string',
                    'limit'    => 100,
                    'nullable' => true,
                ],
                'new_col'                 => [
                    'type' => 'string',
                ],
                'comment'                 => [
                    'type'    => 'timestamp',
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => 'Time is fleeting',
                ],
                'float'                   => [
                    'type'      => 'float',
                    'default'   => '5.4',
                    'scale'     => 12,
                    'precision' => 10,
                ],
            ],
            'indexes'     => [],
            'foreignKeys' => [],
        ];

        $this->beConstructedWith($this->fileTest, $this->dbTest);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CWSpear\SchemaDefinition\Differ\Differ');
        $this->shouldImplement('CWSpear\SchemaDefinition\Differ\DifferInterface');
    }

    function it_should_perform_schema_diffs_up()
    {
        // represents "up"
        $this->diff($this->fileTest, $this->dbTest)->shouldReturn([
            'fields' => [
                'the_real_id'             => [
                    'type'     => 'integer',
                    'limit'    => 10,
                    'unsigned' => 'unsigned',
                ],
                'signed_int_null_default' => [
                    'type'     => 'integer',
                    'limit'    => 5,
                    'nullable' => true,
                ],
                '200_char_null'           => [
                    'type'     => 'string',
                    'limit'    => 100,
                    'nullable' => true,
                ],
                'new_col'                 => [
                    'type' => 'string',
                ],
                'float'                   => [
                    'type'      => 'float',
                    'default'   => '5.4',
                    'scale'     => 12,
                    'precision' => 10,
                ],
            ],
        ]);
    }

    function it_should_perform_schema_diffs_down()
    {
        // represents "down"
        $this->diff($this->dbTest, $this->fileTest)->shouldReturn([
            'fields' => [
                'second_id'               => [
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
                '100_char'                => [
                    'type'  => 'string',
                    'limit' => 100,
                ],
                '200_char_null'           => [
                    'type'     => 'string',
                    'limit'    => 200,
                    'nullable' => true,
                ],
                'float'                   => [
                    'type'      => 'float',
                    'scale'     => 12,
                    'precision' => 10,
                ],
            ],
        ]);
    }

    function it_should_determine_if_something_is_being_added()
    {
        $this->getAdded()->shouldReturn([
            'fields' => [
                'the_real_id' => [
                    'type'     => 'integer',
                    'limit'    => 10,
                    'unsigned' => 'unsigned',
                ],
                'new_col'     => [
                    'type' => 'string',
                ],
            ],
        ]);
    }

    function it_should_determine_if_something_is_being_removed()
    {
        $this->getRemoved()->shouldReturn([
            'fields' => [
                'second_id' => [
                    'type'     => 'integer',
                    'limit'    => 10,
                    'unsigned' => 'unsigned',
                ],
                '100_char'  => [
                    'type'  => 'string',
                    'limit' => 100,
                ],
            ],
        ]);
    }

    function it_should_determine_if_something_is_being_altered_up()
    {
        $this->getAlteredUp()->shouldReturn([
            'fields' => [
                'signed_int_null_default' => [
                    'type'     => 'integer',
                    'limit'    => 5,
                    'nullable' => true,
                ],
                '200_char_null'           => [
                    'type'     => 'string',
                    'limit'    => 100,
                    'nullable' => true,
                ],
                'float'                   => [
                    'type'      => 'float',
                    'default'   => '5.4',
                    'scale'     => 12,
                    'precision' => 10,
                ],
            ],
        ]);
    }

    function it_should_determine_if_something_is_being_altered_down()
    {
        $this->getAlteredDown()->shouldReturn([
            'fields' => [
                'signed_int_null_default' => [
                    'type'     => 'integer',
                    'limit'    => 5,
                    'default'  => '3',
                    'nullable' => true,
                ],
                '200_char_null'           => [
                    'type'     => 'string',
                    'limit'    => 200,
                    'nullable' => true,
                ],
                'float'                   => [
                    'type'      => 'float',
                    'scale'     => 12,
                    'precision' => 10,
                ],
            ],
        ]);
    }
}

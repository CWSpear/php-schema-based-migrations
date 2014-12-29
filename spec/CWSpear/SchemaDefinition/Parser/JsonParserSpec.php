<?php namespace spec\CWSpear\SchemaDefinition\Parser;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin JsonParser
 */
class JsonParserSpec extends ObjectBehavior
{
    protected $array;
    protected $json;

    function let()
    {
        $this->array = [
            'name' => 'Cameron',
            'array' => ['one', 'two', 'three'],
            'object' => ['one' => 1, 'two' => 2],
        ];

        $this->json = <<<JSON
{
    "name": "Cameron",
    "array": [
        "one",
        "two",
        "three"
    ],
    "object": {
        "one": 1,
        "two": 2
    }
}
JSON;
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CWSpear\SchemaDefinition\Parser\JsonParser');
    }

    function it_should_return_an_appropriate_ext()
    {
        $this->getExt()->shouldReturn('json');
    }

    function it_should_parse_json_to_an_array()
    {
        $this->parse($this->json)->shouldReturn($this->array);
    }

    function it_should_serialize_an_array_from_json()
    {
        $this->serialize($this->array)->shouldReturn($this->json);
    }

    function it_should_return_itself_if_going_both_ways()
    {
        $this->serialize($this->parse($this->json))->shouldReturn($this->json);
        $this->parse($this->serialize($this->array))->shouldReturn($this->array);
    }
}

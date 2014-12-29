<?php namespace spec\CWSpear\SchemaDefinition\Differ;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DifferSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([], []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CWSpear\SchemaDefinition\Differ\Differ');
        $this->shouldImplement('CWSpear\SchemaDefinition\Differ\DifferInterface');
    }
}

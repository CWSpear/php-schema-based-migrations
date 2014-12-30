<?php namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module;

class FunctionalHelper extends Module
{
    protected $actual   = 'tests/_fixtures/actual';
    protected $expected = 'tests/_fixtures/expected';

    public function seeActualFileMatchesExpected($filename)
    {
        $this->assertEquals(file_get_contents("{$this->actual}/$filename"), file_get_contents("{$this->expected}/$filename"));
    }

    public function seeFileWasCreated($filename)
    {
        $this->assertTrue(file_exists("{$this->actual}/$filename"));
    }
}

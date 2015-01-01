<?php namespace spec\CWSpear\SchemaDefinition\Filesystem;

use CWSpear\SchemaDefinition\Differ\DifferInterface;
use CWSpear\SchemaDefinition\Filesystem\Filesystem;
use CWSpear\SchemaDefinition\Generator\GeneratorInterface;
use CWSpear\SchemaDefinition\Parser\ParserInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * @mixin Filesystem
 */
class FilesystemSpec extends ObjectBehavior
{
    function let(ParserInterface $parser, GeneratorInterface $generator, SymfonyFilesystem $filesystem)
    {
        $this->beConstructedWith(
            $parser,
            $generator,
            './tests/_fixtures/expected/schemas',
            './tests/_fixtures/expected/migrations'
        );

        $this->filesystem = $filesystem;
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CWSpear\SchemaDefinition\Filesystem\Filesystem');
        $this->shouldImplement('CWSpear\SchemaDefinition\Filesystem\FilesystemInterface');
    }

    function it_should_save_schema(SymfonyFilesystem $filesystem, ParserInterface $parser)
    {
        $schema = ['schema'];
        $schemaStr = '["schema"]';
        $table = 'banana';

        $parser->serialize($schema)->shouldBeCalled()->willReturn($schemaStr);
        $parser->getExt()->shouldBeCalled()->willReturn('json');
        $filesystem->dumpFile('./tests/_fixtures/expected/schemas/banana.json', $schemaStr);

        $this->saveSchema($table, $schema);
    }

    function it_should_save_migrations(SymfonyFilesystem $filesystem, GeneratorInterface $generator, DifferInterface $differ)
    {
        $table = 'banana';

        $generatedName = 'mega_man';
        $dummyStr = 'This is a migration';

        $generator->generateName()->shouldBeCalled()->willReturn($generatedName);
        $generator->generateMigration($table, $differ)->shouldBeCalled()->willReturn($dummyStr);

        $filesystem->dumpFile("./tests/_fixtures/expected/migrations/{$generatedName}.php", $dummyStr)->shouldBeCalled();

        $this->saveMigration($table, $differ);
    }

    function it_should_load_schema_from_file(ParserInterface $parser)
    {
        $schema = ['schema'];
        $schemaStr = '["schema"]';
        $table = 'banana';

        $parser->parse($schemaStr)->shouldBeCalled()->willReturn($schema);
        $parser->getExt()->shouldBeCalled()->willReturn('json');

        $this->loadSchema($table)->shouldReturn($schema);
    }
}

<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('export existing schema from the database to a file');

// need --no-ansi to be able to see output!
$I->runShellCommand('commandthatmustnotbenamed export -c ./tests/_fixtures/schema.yml --no-ansi');

$I->seeInShellOutput('[diverse] successfully exported');
$I->seeInShellOutput('[second] successfully exported');

$I->seeFileWasCreated('schemas/diverse.json');
$I->seeFileWasCreated('schemas/second.json');
$I->seeActualFileMatchesExpected('schemas/diverse.json');
$I->seeActualFileMatchesExpected('schemas/second.json');

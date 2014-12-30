<?php
// Here you can initialize variables that will be available to your tests

function deleteFilesInDir($dir)
{
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        if (!$file->isDir()) {
            unlink($file->getRealPath());
        }
    }

    touch("{$dir}/.gitkeep");
}

deleteFilesInDir('./tests/_fixtures/actual');

<?php namespace CWSpear\SchemaDefinition\Parser;

interface ParserInterface
{
    /**
     * Covert a string to an array
     *
     * @param string $str
     * @return array
     */
    public function parse($str);

    /**
     * Convert an array to a serialized string
     *
     * @param array $array
     * @return string
     */
    public function serialize(array $array);

    /**
     * Get the extension that goes with the format of this parser
     *
     * @return string
     */
    public function getExt();
}
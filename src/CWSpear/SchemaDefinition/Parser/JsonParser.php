<?php namespace CWSpear\SchemaDefinition\Parser;

class JsonParser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($str)
    {
        return json_decode($str, true);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(array $array)
    {
        return json_encode($array, JSON_PRETTY_PRINT);
    }

    /**
     * {@inheritdoc}
     */
    public function getExt()
    {
        return 'json';
    }
}

<?php namespace CWSpear\SchemaDefinition\Differ;

class Differ implements DifferInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $db, array $file)
    {
        // TODO: Implement __construct() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getAdded()
    {
        // TODO: Implement getAdded() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoved()
    {
        // TODO: Implement getRemoved() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getAltered()
    {
        // TODO: Implement getAltered() method.
    }

    /**
     * {@inheritdoc}
     * @see http://php.net/manual/en/function.array-diff-assoc.php#111675
     */
    public function diff(array $origin, array $destination)
    {
        $difference = [];
        foreach ($origin as $key => $value) {
            if (is_array($value)) {
                if (!isset($destination[$key]) || !is_array($destination[$key])) {
                    $difference[$key] = $value;
                } else {
                    $newDiff = $this->diff($value, $destination[$key]);
                    if (!empty($newDiff)) {
                        $difference[$key] = $newDiff;
                    }
                }
            } elseif (!array_key_exists($key, $destination) || $destination[$key] !== $value) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }
}

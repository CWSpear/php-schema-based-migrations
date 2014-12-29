<?php namespace CWSpear\SchemaDefinition\Db;


use PDOException;
use PDO;

class AbstractAdapter
{
    /**
     * @var PDO
     */
    protected $db;

    /**
     * Wrapper to handle errors and preparing.
     *
     * @param string $sql
     * @param array  $bindings
     * @return array
     */
    public function query($sql, array $bindings = null)
    {
        if (!is_null($bindings)) {
            $result = $this->db->prepare($sql);
            $result->execute($bindings);
        } else {
            $result = $this->db->query($sql);
        }

        if ($result === false) {
            throw new PDOException(implode('. ', $this->db->errorInfo()), $this->db->errorCode());
        }

        return $result->fetchAll();
    }
}
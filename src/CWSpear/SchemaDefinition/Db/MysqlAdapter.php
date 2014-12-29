<?php namespace CWSpear\SchemaDefinition\Db;

use PDO;

class MysqlAdapter extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var PDO
     */
    public $db;

    /**
     * {@inheritdoc}
     */
    public function __construct($host, $username, $password, $database)
    {
        $this->db = new PDO("mysql:dbname={$database};host={$host}", $username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function getTables()
    {
        $rows = $this->query('SHOW TABLES');

        return array_map(function ($row) {
            return $row[0];
        }, $rows);
    }

    //'limit'      => null,
    //'null'       => false,
    //'default'    => null,
    //'identity'   => false,
    //'after'      => null,
    //'update'     => null,
    //'precision'  => null,
    //'scale'      => null,
    //'comment'    => null,
    //'signed'     => true,
    //'properties' => [],

    /**
     * {@inheritdoc}
     */
    public function getFields($table)
    {
        $rows = $this->query("SHOW FULL COLUMNS FROM `{$table}`");

        $columns = array_map(function ($row) {
            $data = $this->extractData($row['Type']);

            return array_filter([
                'name'      => $row['Field'] ?: null,
                'type'      => $data['type'] ?: null,
                'limit'     => isset($data['limit']) ? intval($data['limit']) : null,
                'default'   => $row['Default'] ?: null,
                'nullable'  => $row['Null'] === 'YES' ?: null,
                'identity'  => $row['Key'] === 'PRI' ?: null,
                'scale'     => isset($data['scale']) ? intval($data['scale']) : null,
                'precision' => isset($data['precision']) ? intval($data['precision']) : null,
                'comment'   => $row['Comment'] ?: null,
                'unsigned'  => $data['unsigned'] ?: null,
                'update'    => null,
                'after'     => null,
            ], function ($item) {
                return !is_null($item);
            });
        }, $rows);

        $fields = [];

        foreach ($columns as $column) {
            $name = $column['name'];
            unset($column['name']);
            $fields[$name] = $column;
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexes($table)
    {
        $rows = $this->query("SHOW INDEXES FROM `{$table}`");

        $indexes = [];
        foreach ($rows as $row) {
            if (!isset($indexes[$row['Key_name']])) {
                $indexes[$row['Key_name']] = ['columns' => []];
            }
            $indexes[$row['Key_name']]['columns'][] = strtolower($row['Column_name']);
            $indexes[$row['Key_name']]['unique']    = !$row['Non_unique'];
        }

        return array_values($indexes);
    }

    /**
     * {@inheritdoc}
     */
    public function getForeignKeys($table)
    {
        $sql = 'SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME ';
        $sql .= 'FROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL AND TABLE_NAME = ';
        $sql .= "'{$table}'";

        $rows = $this->query($sql);

        return array_map(function ($row) {
            return [
                'column'         => $row['COLUMN_NAME'],
                'foreign_table'  => $row['TABLE_NAME'],
                'foreign_column' => $row['REFERENCED_COLUMN_NAME'],
            ];
        }, $rows);
    }

    /**
     * Tbe Type column in SHOW COLUMNS has info on a number
     * of options we use and this method extracts those to an array
     *
     * @param string $typeCol
     * @return array
     */
    protected function extractData($typeCol)
    {
        preg_match('/([a-z]+)\(?(\d+) ?,?(\d+)?\)? ?(unsigned)?/', $typeCol, $matches);
        $type     = isset($matches[1]) ? $matches[1] : $typeCol;
        $num1     = isset($matches[2]) ? $matches[2] : null;
        $num2     = isset($matches[3]) ? $matches[3] : null;
        $unsigned = isset($matches[4]) ? $matches[4] : null;

        $ret = ['type' => $this->normalizeFieldType($type)];

        if ($num2) {
            if (in_array($type, ['double', 'float', 'decimal', 'numeric'])) {
                $ret['limit']     = null;
                $ret['precision'] = $num2;
                $ret['scale']     = $num1;
            }
        } else {
            $ret['limit']     = $num1 ?: null;
            $ret['precision'] = null;
            $ret['scale']     = null;
        }

        $ret['unsigned'] = $unsigned ?: null;

        return $ret;
    }

    /**
     * Some columns have a different name than the ones we use.
     * This method normalizes those names (i.e. int -> integer)
     *
     * @param string $type
     * @return string
     */
    protected function normalizeFieldType($type)
    {
        if (preg_match(' /int$/', $type)) {
            $type .= 'eger';

            return $type;
        }

        if ($type === 'varchar') {
            return 'string';
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($column, $table)
    {
        // TODO: caching?
        return in_array($column, array_keys($this->getFields($table)));
    }

    /**
     * {@inheritdoc}
     */
    public function hasTable($table)
    {
        // TODO: caching?
        return in_array($table, $this->getTables());
    }
}

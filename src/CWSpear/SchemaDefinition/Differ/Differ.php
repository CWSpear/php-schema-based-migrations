<?php namespace CWSpear\SchemaDefinition\Differ;

use Exception;

class Differ implements DifferInterface
{
    /**
     * @var array
     */
    protected $db;

    /**
     * @var array
     */
    protected $file;

    /**
     * @var array
     */
    protected $up;

    /**
     * @var array
     */
    protected $down;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $file, array $db)
    {
        $this->db   = $db;
        $this->file = $file;
        $this->up   = $this->diff($file, $db);
        $this->down = $this->diff($db, $file);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdded()
    {
        // local copy of up
        $added = $this->up;

        foreach ($added[self::FIELDS] as $col => $options) {
            // cols that exist ONLY in the up array
            // are columns that are being added
            if (array_key_exists($col, $this->down[self::FIELDS])) {
                unset($added[self::FIELDS][$col]);
            }
        }

        return $this->added = $added;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoved()
    {
        // local copy of up (doesn't really matter where we start)
        $removed = $this->down;

        foreach ($removed[self::FIELDS] as $col => $options) {
            // cols that exist ONLY in the down array
            // are columns that are being removed
            if (array_key_exists($col, $this->up[self::FIELDS])) {
                unset($removed[self::FIELDS][$col]);
            }
        }

        return $this->removed = $removed;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlteredUp()
    {
        // local copy of up
        $alteredUp = $this->up;

        foreach ($alteredUp[self::FIELDS] as $col => $options) {
            // cols that are in both up and down
            // are columns that are being altered
            if (!array_key_exists($col, $this->down[self::FIELDS])) {
                // so if it does not exist in the other one,
                // we don't want it as it's not "altered"
                unset($alteredUp[self::FIELDS][$col]);
            }
        }

        return $alteredUp;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlteredDown()
    {
        // local copy of down
        $alteredDown = $this->down;

        foreach ($alteredDown[self::FIELDS] as $col => $options) {
            // cols that are in both up and down
            // are columns that are being altered
            if (!array_key_exists($col, $this->up[self::FIELDS])) {
                // so if it does not exist in the other one,
                // we don't want it as it's not "altered"
                unset($alteredDown[self::FIELDS][$col]);
            }
        }

        return $alteredDown;
    }

    /**
     * {@inheritdoc}
     */
    public function diff(array $origin, array $destination)
    {
        $difference = [];
        foreach ($origin as $type => $value) {
            foreach ($value as $k => $v) {
                if (!isset($destination[$type][$k])) {
                    $difference[$type][$k] = $v;
                } else {
                    $diff  = array_diff($v, $destination[$type][$k]);
                    $diff2 = array_diff($destination[$type][$k], $v);
                    if (!empty($diff) || !empty($diff2)) {
                        $difference[$type][$k] = $v;
                    }
                }
            }
        }

        return $difference;
    }
}

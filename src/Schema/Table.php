<?php

namespace Rougin\Ezekiel\Schema;

use Rougin\Ezekiel\Dialect\MysqlDialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Table
{
    const TYPE_CREATE = 0;

    const TYPE_DROP = 1;

    const TYPE_DROP_IF_EXISTS = 2;

    const TYPE_TABLE = 3;

    /**
     * @var \Rougin\Ezekiel\Schema\Design|null
     */
    protected $design = null;

    /**
     * @var \Rougin\Ezekiel\DialectInterface
     */
    protected $dialect;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @param \Rougin\Ezekiel\DialectInterface $dialect
     */
    public function __construct($dialect = null)
    {
        if ($dialect === null)
        {
            $dialect = new MysqlDialect;
        }

        $this->dialect = $dialect;
    }

    /**
     * @param string   $table
     * @param callable $callback
     *
     * @return self
     */
    public function create($table, $callback)
    {
        $this->type = self::TYPE_CREATE;

        $this->table = $table;

        $design = new Design;

        call_user_func($callback, $design);

        $this->design = $design;

        return $this;
    }

    /**
     * @param string $table
     *
     * @return self
     */
    public function drop($table)
    {
        $this->type = self::TYPE_DROP;

        $this->table = $table;

        return $this;
    }

    /**
     * @param string $table
     *
     * @return self
     */
    public function dropIfExists($table)
    {
        $this->type = self::TYPE_DROP_IF_EXISTS;

        $this->table = $table;

        return $this;
    }

    /**
     * @param string   $table
     * @param callable $callback
     *
     * @return self
     */
    public function table($table, $callback)
    {
        $this->type = self::TYPE_TABLE;

        $this->table = $table;

        $design = new Design;

        call_user_func($callback, $design);

        $this->design = $design;

        return $this;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $table = $this->dialect->quote($this->table);

        if ($this->type === self::TYPE_DROP)
        {
            return 'DROP TABLE ' . $table;
        }

        if ($this->type === self::TYPE_DROP_IF_EXISTS)
        {
            return 'DROP TABLE IF EXISTS ' . $table;
        }

        if ($this->type === self::TYPE_TABLE)
        {
            return $this->compileAlter($table);
        }

        return $this->compileCreate($table);
    }

    /**
     * @param string $table
     *
     * @return string
     */
    protected function compileAlter($table)
    {
        $sql = 'ALTER TABLE ' . $table;

        if ($this->design)
        {
            $columns = $this->design->compile($this->dialect);

            $lines = explode(', ', $columns);

            $items = array();

            foreach ($lines as $line)
            {
                $items[] = 'ADD ' . $line;
            }

            $sql .= ' ' . implode(', ', $items);
        }

        return $sql;
    }

    /**
     * @param string $table
     *
     * @return string
     */
    protected function compileCreate($table)
    {
        $sql = 'CREATE TABLE ' . $table . ' (';

        if ($this->design)
        {
            $sql .= $this->design->compile($this->dialect);
        }

        return $sql . ')';
    }
}

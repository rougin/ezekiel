<?php

namespace Rougin\Ezekiel\Dialect;

use Rougin\Ezekiel\Schema\Column;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SqliteDialect extends AbstractDialect
{
    /**
     * @return boolean
     */
    public function canRightJoin()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sqlite';
    }

    /**
     * @return string
     */
    public function getOpenQuoteChar()
    {
        return '"';
    }

    /**
     * @param string $table
     * @param string $columns
     *
     * @return string
     */
    public function toAlterTable($table, $columns)
    {
        $table = $this->quote($table);

        $sql = 'ALTER TABLE ' . $table;

        if ($columns !== '')
        {
            $lines = explode(', ', $columns);

            $items = array();

            foreach ($lines as $line)
            {
                $items[] = 'ADD COLUMN ' . $line;
            }

            $sql .= ' ' . implode(', ', $items);
        }

        return $sql;
    }

    /**
     * @param \Rougin\Ezekiel\Schema\Column $column
     *
     * @return string
     */
    public function toColumn(Column $column)
    {
        $type = $column->getType();

        $length = $column->getLength();

        if ($type === 'INT' || $type === 'TINYINT')
        {
            $type = 'INTEGER';

            $length = null;
        }

        if ($type === 'DATETIME')
        {
            $type = 'TEXT';

            $length = null;
        }

        $sql = $this->quote($column->getName());

        $sql .= ' ' . $type;

        if ($length !== null)
        {
            $sql .= '(' . $length . ')';
        }

        if (! $column->isNullable())
        {
            $sql .= ' NOT NULL';
        }

        if ($column->hasDefault())
        {
            $default = $column->getDefault();

            if (is_bool($default))
            {
                $sql .= ' DEFAULT ' . (int) $default;
            }

            if (is_int($default) || is_float($default))
            {
                $sql .= ' DEFAULT ' . $default;
            }

            if (is_string($default))
            {
                $sql .= ' DEFAULT \'' . $default . '\'';
            }
        }

        if ($column->isUnique())
        {
            $sql .= ' UNIQUE';
        }

        if ($column->isPrimary())
        {
            $sql .= ' PRIMARY KEY';
        }

        if ($column->isAutoIncrement())
        {
            $sql .= ' AUTOINCREMENT';
        }

        return $sql;
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function toLimit($limit, $offset)
    {
        return ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }
}

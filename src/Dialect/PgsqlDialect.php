<?php

namespace Rougin\Ezekiel\Dialect;

use Rougin\Ezekiel\Schema\Column;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class PgsqlDialect extends AbstractDialect
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'pgsql';
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

        if ($type === 'TINYINT')
        {
            $type = 'SMALLINT';
        }

        if ($type === 'DATETIME')
        {
            $type = 'TIMESTAMP';
        }

        if ($type === 'TINYINT' && $length === 1)
        {
            $type = 'BOOLEAN';

            $length = null;
        }

        if ($column->isAutoIncrement() && $type === 'INT')
        {
            $type = 'SERIAL';

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
                $sql .= ' DEFAULT ' . ($default ? 'TRUE' : 'FALSE');
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

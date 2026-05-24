<?php

namespace Rougin\Ezekiel\Dialect;

use Rougin\Ezekiel\Schema\Column;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class MssqlDialect extends AbstractDialect
{
    /**
     * @return string
     */
    public function getCloseQuoteChar()
    {
        return ']';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mssql';
    }

    /**
     * @return string
     */
    public function getOpenQuoteChar()
    {
        return '[';
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

        if ($type === 'TINYINT' && $length === 1)
        {
            $type = 'BIT';

            $length = null;
        }

        if ($type === 'TINYINT' && $length !== 1)
        {
            $type = 'SMALLINT';
        }

        if ($type === 'TINYINT' && $length === 1)
        {
            $type = 'BIT';

            $length = null;
        }

        $sql = $this->quote($column->getName());

        $sql .= ' ' . $type;

        if ($length !== null)
        {
            $sql .= '(' . $length . ')';
        }

        if ($column->isAutoIncrement())
        {
            $sql .= ' IDENTITY(1,1)';
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
        return ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $limit . ' ROWS ONLY';
    }
}

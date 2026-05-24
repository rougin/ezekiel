<?php

namespace Rougin\Ezekiel\Dialect;

use Rougin\Ezekiel\DialectInterface;
use Rougin\Ezekiel\Schema\Column;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
abstract class AbstractDialect implements DialectInterface
{
    /**
     * @return boolean
     */
    public function canRightJoin()
    {
        return true;
    }

    /**
     * Returns the opening quote character.
     *
     * @return string
     */
    abstract public function getOpenQuoteChar();

    /**
     * Returns the closing quote character. Defaults to the
     * open character for dialects with symmetric quoting.
     *
     * @return string
     */
    public function getCloseQuoteChar()
    {
        return $this->getOpenQuoteChar();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function quote($name)
    {
        $close = $this->getCloseQuoteChar();

        $open = $this->getOpenQuoteChar();

        if (strlen($name) > 0 && $name[0] === $open)
        {
            return $name;
        }

        if ($name === '*')
        {
            return $name;
        }

        if (strpos($name, '(') !== false)
        {
            return $name;
        }

        if (is_numeric($name[0]))
        {
            return $name;
        }

        if (strpos($name, '.') !== false)
        {
            $parts = explode('.', $name);

            foreach ($parts as &$part)
            {
                if ($part !== '*')
                {
                    $part = $open . $part . $close;
                }
            }

            return implode('.', $parts);
        }

        if (strpos($name, ' ') !== false)
        {
            $parts = explode(' ', $name);

            $table = $open . $parts[0] . $close;

            $rest = array();

            $count = count($parts);

            for ($i = 1; $i < $count; $i++)
            {
                $p = $parts[$i];

                if ($p === 'AS' || $p === 'as' || $p === '')
                {
                    $rest[] = $p;

                    continue;
                }

                $rest[] = $open . $p . $close;
            }

            return $table . ' ' . implode(' ', $rest);
        }

        return $open . $name . $close;
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
                $items[] = 'ADD ' . $line;
            }

            $sql .= ' ' . implode(', ', $items);
        }

        return $sql;
    }

    /**
     * @param Column $column
     *
     * @return string
     */
    public function toColumn(Column $column)
    {
        $sql = $this->quote($column->getName());

        $sql .= ' ' . $column->getType();

        $length = $column->getLength();

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

        if ($column->isAutoIncrement())
        {
            $sql .= ' AUTO_INCREMENT';
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
     * @param string $table
     * @param string $columns
     *
     * @return string
     */
    public function toCreateTable($table, $columns)
    {
        $table = $this->quote($table);

        return 'CREATE TABLE ' . $table . ' (' . $columns . ')';
    }

    /**
     * @param string $table
     *
     * @return string
     */
    public function toDropTable($table)
    {
        return 'DROP TABLE ' . $this->quote($table);
    }

    /**
     * @param string $table
     *
     * @return string
     */
    public function toDropTableIfExists($table)
    {
        return 'DROP TABLE IF EXISTS ' . $this->quote($table);
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function toLimit($limit, $offset)
    {
        return ' LIMIT ' . $limit . ', ' . $offset;
    }
}

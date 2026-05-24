<?php

namespace Rougin\Ezekiel\Query;

use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\QueryInterface;
use Rougin\Ezekiel\ValueInterface;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Insert implements QueryInterface, ValueInterface
{
    /**
     * @var boolean
     */
    protected $batch = false;

    /**
     * @var mixed[]
     */
    protected $batches = array();

    /**
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @var array<int, array<string, mixed>>|array<string, mixed>
     */
    protected $values = array();

    /**
     * @param \Rougin\Ezekiel\Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return Query::TYPE_INSERT;
    }

    /**
     * @return mixed[]
     */
    public function getValues()
    {
        if ($this->batch)
        {
            return $this->batches;
        }

        return $this->values;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $dialect = $this->query->getDialect();

        if ($this->batch)
        {
            return $this->toBatchSql($dialect);
        }

        // Create placeholders for all values ------------
        $items = array_fill(0, count($this->values), '?');

        $values = '(' . implode(', ', $items) . ')';
        // -----------------------------------------------

        // Extract specified fields ------------------
        $keys = array();

        foreach (array_keys($this->values) as $key)
        {
            $keys[] = $dialect->quote((string) $key);
        }

        $keys = '(' . implode(', ', $keys) . ')';
        // -------------------------------------------

        $table = $this->query->getTable();

        $table = $dialect->quote($table);

        $sql = 'INSERT INTO ' . $table;

        return $sql . ' ' . $keys . ' VALUES ' . $values;
    }

    /**
     * @param array<int, array<string, mixed>>|array<string, mixed> $values
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function values($values)
    {
        $first = reset($values);

        if (is_array($first) && ! is_string(key($values)))
        {
            $this->batch = true;

            $this->batches = array();

            /** @var array<string, mixed>[] $values */
            foreach ($values as $row)
            {
                foreach ($row as $val)
                {
                    $this->batches[] = $val;
                }
            }
        }

        $this->values = $values;

        return $this->query->addItem($this);
    }

    /**
     * @param \Rougin\Ezekiel\DialectInterface $dialect
     *
     * @return string
     */
    protected function toBatchSql($dialect)
    {
        /** @var array<string, mixed>[] */
        $rows = $this->values;

        $firstRow = $rows[0];

        $keys = array();

        /** @var string $key */
        foreach (array_keys($firstRow) as $key)
        {
            $keys[] = $dialect->quote($key);
        }

        $keys = '(' . implode(', ', $keys) . ')';

        $placeholders = array();

        foreach ($rows as $row)
        {
            $items = array_fill(0, count($row), '?');

            $placeholders[] = '(' . implode(', ', $items) . ')';
        }

        $values = implode(', ', $placeholders);

        $table = $this->query->getTable();

        $table = $dialect->quote($table);

        return 'INSERT INTO ' . $table . ' ' . $keys . ' VALUES ' . $values;
    }
}

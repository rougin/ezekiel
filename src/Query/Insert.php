<?php

namespace Rougin\Ezekiel\Query;

use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\QueryInterface;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Insert implements QueryInterface
{
    /**
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @var array<string, mixed>
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
     * @return array<string, mixed>
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $dialect = $this->query->getDialect();

        // Create placeholders for all values ------------
        $items = array_fill(0, count($this->values), '?');

        $values = '(' . implode(', ', $items) . ')';
        // -----------------------------------------------

        // Extract specified fields ------------------
        $keys = array();

        foreach (array_keys($this->values) as $key)
        {
            $keys[] = $dialect->quote($key);
        }

        $keys = '(' . implode(', ', $keys) . ')';
        // -------------------------------------------

        $table = $this->query->getTable();

        $table = $dialect->quote($table);

        $sql = 'INSERT INTO ' . $table;

        return $sql . ' ' . $keys . ' VALUES ' . $values;
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function values($values)
    {
        $this->values = $values;

        return $this->query->addItem($this);
    }
}

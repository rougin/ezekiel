<?php

namespace Rougin\Ezekiel;

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
    protected $values;

    /**
     * @param \Rougin\Ezekiel\Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
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
        // Create placeholders for all values ------------
        $items = array_fill(0, count($this->values), '?');

        $values = '(' . implode(', ', $items) . ')';
        // -----------------------------------------------

        // Extract specified fields -------------
        $keys = array_keys($this->values);

        $keys = '(' . implode(', ', $keys) . ')';
        // --------------------------------------

        $sql = 'INSERT INTO ' . $this->query->getTable();

        return $sql . ' ' . $keys . ' VALUES ' . $values;
    }
}

<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Select implements QueryInterface
{
    /**
     * @var string|null
     */
    protected $alias = null;

    /**
     * @var string[]
     */
    protected $fields = array();

    /**
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param string|string[]       $fields
     */
    public function __construct(Query $query, $fields)
    {
        if (is_string($fields))
        {
            $fields = array($fields);
        }

        $this->fields = $fields;

        $this->query = $query;
    }

    /**
     * @param string $table
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function from($table)
    {
        $this->table = $table;

        return $this->query->addItem($this);
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return Query::TYPE_SELECT;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValues()
    {
        return array();
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $sql = 'SELECT ' . implode(', ', $this->fields);

        $alias = $this->alias ? ' ' . $this->alias : '';

        return $sql . ' FROM ' . $this->table . $alias;
    }

    /**
     * @param string $alias
     *
     * @return self
     */
    public function withAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }
}

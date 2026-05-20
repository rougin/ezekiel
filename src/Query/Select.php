<?php

namespace Rougin\Ezekiel\Query;

use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\QueryInterface;

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
     * @var mixed[]
     */
    protected $binds = array();

    /**
     * @var boolean
     */
    protected $distinct = false;

    /**
     * @var string[]
     */
    protected $fields = array();

    /**
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @var \Rougin\Ezekiel\Query|string
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
     * @return self
     */
    public function distinct()
    {
        $this->distinct = true;

        return $this;
    }

    /**
     * @param \Rougin\Ezekiel\Query|string $table
     * @param string|null                  $alias
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function from($table, $alias = null)
    {
        if ($alias !== null)
        {
            $this->alias = $alias;
        }

        $this->table = $table;

        return $this->query->addItem($this);
    }

    /**
     * @return mixed[]
     */
    public function getSubqueryBinds()
    {
        return $this->binds;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return Query::TYPE_SELECT;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $dialect = $this->query->getDialect();

        $fields = array();

        foreach ($this->fields as $field)
        {
            if (strpos($field, ',') === false)
            {
                $fields[] = $dialect->quote($field);

                continue;
            }

            $parts = array_map('trim', explode(',', $field));

            $keys = array();

            foreach ($parts as $part)
            {
                $keys[] = $dialect->quote($part);
            }

            $fields[] = implode(', ', $keys);
        }

        $keyword = $this->distinct ? 'SELECT DISTINCT ' : 'SELECT ';

        $sql = $keyword . implode(', ', $fields);

        $alias = $this->alias ? ' ' . $this->alias : '';

        $table = $this->table;

        if ($table instanceof Query)
        {
            $subSql = $table->toSql();

            $this->binds = $table->getBinds();

            $table = '(' . $subSql . ')';
        }
        else
        {
            $table = $dialect->quote($table);
        }

        return $sql . ' FROM ' . $table . $alias;
    }
}

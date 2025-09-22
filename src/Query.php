<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Query
{
    const TYPE_SELECT = 0;

    const TYPE_INSERT = 1;

    const TYPE_UPDATE = 2;

    const TYPE_DELETE = 3;

    const TYPE_WHERE = 4;

    const TYPE_HAVING = 5;

    /**
     * @var string|null
     */
    protected $alias = null;

    /**
     * @var string[]
     */
    protected $fields = array();

    /**
     * @var \Rougin\Ezekiel\QueryInterface[]
     */
    protected $items = array();

    /**
     * @var string
     */
    protected $table;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @param \Rougin\Ezekiel\QueryInterface $query
     *
     * @return self
     */
    public function addItem(QueryInterface $query)
    {
        $this->items[] = $query;

        return $this;
    }

    /**
     * Generates a "SELECT" query.
     *
     * @param string|string[] $fields
     *
     * @return self
     */
    public function select($fields)
    {
        if (is_string($fields))
        {
            $fields = array($fields);
        }

        $this->fields = $fields;

        $this->type = self::TYPE_SELECT;

        return $this;
    }

    /**
     * Generates a "FROM" query.
     *
     * @param string $table
     *
     * @return self
     */
    public function from($table)
    {
        $this->table = $table;

        return $this;
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

    /**
     * Generates an "INNER JOIN" query.
     *
     * @param string      $table
     * @param string      $local
     * @param string      $foreign
     * @param string|null $alias
     *
     * @return self
     */
    public function innerJoin($table, $local, $foreign, $alias = null)
    {
        return $this;
    }

    /**
     * Generates a "LEFT JOIN" query.
     *
     * @param string      $table
     * @param string      $local
     * @param string      $foreign
     * @param string|null $alias
     *
     * @return self
     */
    public function leftJoin($table, $local, $foreign, $alias = null)
    {
        return $this;
    }

    /**
     * Generates a "RIGHT JOIN" query.
     *
     * @param string      $table
     * @param string      $local
     * @param string      $foreign
     * @param string|null $alias
     *
     * @return self
     */
    public function rightJoin($table, $local, $foreign, $alias = null)
    {
        return $this;
    }

    /**
     * Generates an "INSERT INTO" query.
     *
     * @param string $table
     *
     * @return self
     */
    public function insertInto($table)
    {
        return $this;
    }

    /**
     * Generates an "UPDATE" query.
     *
     * @param string      $table
     * @param string|null $alias
     *
     * @return self
     */
    public function update($table, $alias = null)
    {
        return $this;
    }

    /**
     * Generates a "DELETE FROM" query.
     *
     * @param string      $table
     * @param string|null $alias
     *
     * @return self
     */
    public function deleteFrom($table, $alias = null)
    {
        return $this;
    }

    /**
     * Generates a "WHERE" query.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Where
     */
    public function where($key)
    {
        return new Where($this, $key);
    }

    /**
     * Generates an "AND WHERE" query.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Where
     */
    public function andWhere($key)
    {
        $where = new Where($this, $key);

        $where->useAnd();

        return $where;
    }

    /**
     * Generates an "OR WHERE" query.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Where
     */
    public function orWhere($key)
    {
        $where = new Where($this, $key);

        $where->useOr();

        return $where;
    }

    /**
     * Generates a "GROUP BY" query.
     *
     * @param string|string[] $fields
     *
     * @return self
     */
    public function groupBy($fields)
    {
        return $this;
    }

    /**
     * Generates a "HAVING" query.
     *
     * @param string $key
     *
     * @return self
     */
    public function having($key)
    {
        return $this;
    }

    /**
     * Generates an "AND HAVING" query.
     *
     * @param string $key
     *
     * @return self
     */
    public function andHaving($key)
    {
        return $this;
    }

    /**
     * Generates an "OR HAVING" query.
     *
     * @param string $key
     *
     * @return self
     */
    public function orHaving($key)
    {
        return $this;
    }

    /**
     * Generates an "ORDER BY" query.
     *
     * @param string $key
     *
     * @return self
     */
    public function orderBy($key)
    {
        return $this;
    }

    /**
     * Generates multiple "ORDER BY" queries.
     *
     * @param string $key
     *
     * @return self
     */
    public function andOrderBy($key)
    {
        return $this;
    }

    /**
     * Performs a "LIMIT" query.
     *
     * @param integer      $limit
     * @param integer|null $offset
     *
     * @return self
     */
    public function limit($limit, $offset = null)
    {
        return $this;
    }

    /**
     * Returns the SQL bindings specified.
     *
     * @return array<string, string>
     */
    public function getBinds()
    {
        return array();
    }

    /**
     * Returns the safe and compiled SQL.
     *
     * @return string
     */
    public function toSql()
    {
        $sql = 'SELECT ' . implode(', ', $this->fields);

        $sql .= ' FROM "' . $this->table . '"';

        $sql .= $this->alias ? ' ' . $this->alias : '';

        // Queries for "WHERE" ------------------------
        $where = false;

        foreach ($this->items as $item)
        {
            if ($item->getType() !== self::TYPE_WHERE)
            {
                continue;
            }

            if (! $where)
            {
                $sql .= ' WHERE';

                $where = true;
            }

            $sql .= ' ' . $item->toSql();
        }

        $sql = str_replace('WHERE AND', 'WHERE', $sql);
        $sql = str_replace('WHERE OR', 'WHERE', $sql);
        // --------------------------------------------

        return $sql;
    }

    /**
     * Returns the table name from the query.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Returns the type of the query.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }
}

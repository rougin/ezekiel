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

    const TYPE_ORDER = 6;

    /**
     * @var array<string, mixed>
     */
    protected $binds = array();

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
     * Generates an "AND HAVING" query.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Having
     */
    public function andHaving($key)
    {
        return new Having($this, $key, Having::GROUP_AND);
    }

    /**
     * Generates multiple "ORDER BY" queries.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Order
     */
    public function andOrderBy($key)
    {
        $this->type = self::TYPE_ORDER;

        return new Order($this, $key);
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
        return new Where($this, $key, Where::GROUP_AND);
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
     * Returns all SQL bindings.
     *
     * @return array<string, mixed>
     */
    public function getBinds()
    {
        return $this->binds;
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
     * @return \Rougin\Ezekiel\Having
     */
    public function having($key)
    {
        return new Having($this, $key, Having::GROUP_AND);
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
     * Generates an "INSERT INTO" query.
     *
     * @param string $table
     *
     * @return \Rougin\Ezekiel\Insert
     */
    public function insertInto($table)
    {
        $this->type = self::TYPE_INSERT;

        $this->table = $table;

        return new Insert($this);
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
     * Generates an "OR HAVING" query.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Having
     */
    public function orHaving($key)
    {
        return new Having($this, $key, Having::GROUP_OR);
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
        return new Where($this, $key, Where::GROUP_OR);
    }

    /**
     * Generates an "ORDER BY" query.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Order
     */
    public function orderBy($key)
    {
        $this->type = self::TYPE_ORDER;

        return new Order($this, $key);
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
     * Generates a "SELECT" query.
     *
     * @param string|string[] $fields
     *
     * @return \Rougin\Ezekiel\Select
     */
    public function select($fields)
    {
        $this->type = self::TYPE_SELECT;

        return new Select($this, $fields);
    }

    /**
     * Returns the safe and compiled SQL.
     *
     * @return string
     */
    public function toSql()
    {
        $sql = $this->setSelect();

        if ($this->type === self::TYPE_INSERT)
        {
            $sql = $this->setInsert();
        }

        $sql = $this->setWhere($sql);

        $sql = $this->setOrder($sql);

        return $sql;
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
     * @return string
     */
    protected function setInsert()
    {
        foreach ($this->items as $item)
        {
            if ($item instanceof Insert)
            {
                $this->binds = $item->getValues();
            }

            return $item->toSql();
        }

        return '';
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    protected function setOrder($sql)
    {
        $first = false;

        $items = array();

        foreach ($this->items as $item)
        {
            if ($item->getType() !== self::TYPE_ORDER)
            {
                continue;
            }

            $temp = $item->toSql();

            if ($first)
            {
                $temp = str_replace('ORDER BY', '', $temp);
            }

            $items[] = trim($temp);

            $first = true;
        }

        return trim($sql . ' ' . implode(', ', $items));
    }

    /**
     * @return string
     */
    protected function setSelect()
    {
        foreach ($this->items as $item)
        {
            if ($item->getType() !== self::TYPE_SELECT)
            {
                continue;
            }

            return $item->toSql();
        }

        return '';
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    protected function setWhere($sql)
    {
        $first = false;

        foreach ($this->items as $item)
        {
            if ($item->getType() !== self::TYPE_WHERE)
            {
                continue;
            }

            if (! $first)
            {
                $sql .= ' WHERE';

                $first = true;
            }

            $sql .= ' ' . $item->toSql();
        }

        $sql = str_replace('WHERE AND', 'WHERE', $sql);

        return str_replace('WHERE OR', 'WHERE', $sql);
    }
}

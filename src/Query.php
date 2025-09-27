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

    const TYPE_ORDER = 5;

    const TYPE_GROUP = 6;

    const TYPE_HAVING = 7;

    const TYPE_JOIN = 8;

    /**
     * @var string|null
     */
    protected $alias = null;

    /**
     * @var array<string, mixed>
     */
    protected $binds = array();

    /**
     * @var boolean
     */
    protected $entity = false;

    /**
     * @var string[]
     */
    protected $fields = array();

    /**
     * @var string[]
     */
    protected $groups = array();

    /**
     * @var \Rougin\Ezekiel\QueryInterface[]
     */
    protected $items = array();

    /**
     * @var integer
     */
    protected $limit = 0;

    /**
     * @var integer
     */
    protected $offset = 0;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var \Rougin\Ezekiel\Update
     */
    protected $update;

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
     * @param string $table
     *
     * @return self
     */
    public function deleteFrom($table)
    {
        $this->table = $table;

        $this->type = self::TYPE_DELETE;

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
     * Generates a "GROUP BY" query.
     *
     * @param string|string[] $fields
     *
     * @return self
     */
    public function groupBy($fields)
    {
        $this->type = self::TYPE_GROUP;

        if (is_string($fields))
        {
            $fields = array($fields);
        }

        $this->groups = $fields;

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
        return new Having($this, $key);
    }

    /**
     * Generates an "INNER JOIN" query.
     *
     * @param string $table
     *
     * @return \Rougin\Ezekiel\Join
     */
    public function innerJoin($table)
    {
        return new Join($this, $table, Join::TYPE_INNER);
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
     * Checks if the current query is an entity.
     *
     * @return boolean
     */
    public function isEntity()
    {
        return $this->entity;
    }

    /**
     * Generates a "LEFT JOIN" query.
     *
     * @param string $table
     *
     * @return \Rougin\Ezekiel\Join
     */
    public function leftJoin($table)
    {
        return new Join($this, $table, Join::TYPE_LEFT);
    }

    /**
     * Performs a "LIMIT" query.
     *
     * @param integer $limit
     * @param integer $offset
     *
     * @return self
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit = $limit;

        $this->offset = $offset;

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
     * @param string $table
     *
     * @return \Rougin\Ezekiel\Join
     */
    public function rightJoin($table)
    {
        return new Join($this, $table, Join::TYPE_RIGHT);
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
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function set($key, $value)
    {
        $this->update->set($key, $value);

        return $this;
    }

    /**
     * Returns the safe and compiled SQL.
     *
     * @return string
     */
    public function toSql()
    {
        $sql = $this->setSelectSql();

        if ($this->type === self::TYPE_INSERT)
        {
            $sql = $this->setInsertSql();
        }

        if ($this->type === self::TYPE_DELETE)
        {
            $sql = 'DELETE FROM ' . $this->table;
        }

        if ($this->type === self::TYPE_UPDATE)
        {
            $sql = $this->update->toSql();

            $this->binds = $this->update->getValues();
        }

        $sql = $this->setJoinSql($sql);

        $sql = $this->setCompareSql($sql, self::TYPE_WHERE);

        if ($this->limit > 0)
        {
            $sql .= ' LIMIT ' . $this->limit . ', ' . $this->offset;
        }

        if ($this->type === self::TYPE_GROUP)
        {
            $sql .= ' GROUP BY ' . implode(', ', $this->groups);
        }

        $sql = $this->setCompareSql($sql, self::TYPE_HAVING);

        $sql = $this->setOrderSql($sql);

        return $sql;
    }

    /**
     * Generates an "UPDATE" query.
     *
     * @param string $table
     *
     * @return self
     */
    public function update($table)
    {
        $this->type = self::TYPE_UPDATE;

        $this->table = $table;

        $this->update = new Update($this);

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
     * @param string  $sql
     * @param integer $type
     *
     * @return string
     */
    protected function setCompareSql($sql, $type)
    {
        $isHaving = $type === self::TYPE_HAVING;

        $isWhere = $type === self::TYPE_WHERE;

        $first = true;

        $items = array();

        foreach ($this->items as $item)
        {
            // Skip items if not "HAVING" or "WHERE" --------------------------
            $isTypeHaving = $item->getType() === self::TYPE_HAVING;

            $isTypeWhere = $item->getType() === self::TYPE_WHERE;

            if (($isHaving && ! $isTypeHaving) || ($isWhere && ! $isTypeWhere))
            {
                continue;
            }
            // ----------------------------------------------------------------

            if ($item instanceof Compare)
            {
                $values = $item->getValues();

                $this->binds = array_merge($this->binds, $values);
            }

            $temp = $item->toSql();

            $text = $isHaving ? 'HAVING' : 'WHERE';

            if (! $first)
            {
                $temp = str_replace($text . ' ', '', $temp);
            }

            $items[] = trim($temp);

            $first = false;
        }

        return trim($sql . ' ' . implode(' ', $items));
    }

    /**
     * @return string
     */
    protected function setInsertSql()
    {
        $sql = '';

        foreach ($this->items as $item)
        {
            if ($item instanceof Insert)
            {
                $this->binds = $item->getValues();
            }

            $sql = $item->toSql();
        }

        return $sql;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    protected function setJoinSql($sql)
    {
        foreach ($this->items as $item)
        {
            if ($item->getType() !== self::TYPE_JOIN)
            {
                continue;
            }

            $sql .= ' ' . $item->toSql();
        }

        return $sql;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    protected function setOrderSql($sql)
    {
        $first = true;

        $items = array();

        foreach ($this->items as $item)
        {
            if ($item->getType() !== self::TYPE_ORDER)
            {
                continue;
            }

            $temp = $item->toSql();

            if (! $first)
            {
                $temp = str_replace('ORDER BY', '', $temp);
            }

            $items[] = trim($temp);

            $first = false;
        }

        return trim($sql . ' ' . implode(', ', $items));
    }

    /**
     * @return string
     */
    protected function setSelectSql()
    {
        $sql = '';

        foreach ($this->items as $item)
        {
            if ($item->getType() !== self::TYPE_SELECT)
            {
                continue;
            }

            $sql = $item->toSql();
        }

        return $sql;
    }
}

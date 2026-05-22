<?php

namespace Rougin\Ezekiel;

use Rougin\Ezekiel\Dialect\MysqlDialect;
use Rougin\Ezekiel\Query\Compare;
use Rougin\Ezekiel\Query\Having;
use Rougin\Ezekiel\Query\Insert;
use Rougin\Ezekiel\Query\Join;
use Rougin\Ezekiel\Query\Order;
use Rougin\Ezekiel\Query\Select;
use Rougin\Ezekiel\Query\Update;
use Rougin\Ezekiel\Query\Where;
use Rougin\Ezekiel\Query\WhereGroup;

/**
 * @method self                             add_item(\Rougin\Ezekiel\QueryInterface $query)
 * @method \Rougin\Ezekiel\Query\Having     and_having(string $key)
 * @method \Rougin\Ezekiel\Query\Order      and_order_by(string $key)
 * @method \Rougin\Ezekiel\Query\Where      and_where(string $key)
 * @method self                             and_where_group(callable $callback)
 * @method self                             delete_from(string $table)
 * @method array<string, mixed>|mixed[]     get_binds()
 * @method \Rougin\Ezekiel\DialectInterface get_dialect()
 * @method \Rougin\Ezekiel\QueryInterface[] get_items()
 * @method string                           get_table()
 * @method self                             group_by(string|string[] $fields)
 * @method \Rougin\Ezekiel\Query\Join       inner_join(string $table)
 * @method \Rougin\Ezekiel\Query\Insert     insert_into(string $table)
 * @method boolean                          is_entity()
 * @method \Rougin\Ezekiel\Query\Join       left_join(string $table)
 * @method \Rougin\Ezekiel\Query\Having     or_having(string $key)
 * @method \Rougin\Ezekiel\Query\Where      or_where(string $key)
 * @method self                             or_where_group(callable $callback)
 * @method \Rougin\Ezekiel\Query\Order      order_by(string $key)
 * @method \Rougin\Ezekiel\Query\Join       right_join(string $table)
 * @method self                             set_dialect(\Rougin\Ezekiel\DialectInterface $dialect)
 * @method string                           to_sql()
 * @method self                             where_group(callable $callback)
 *
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
     * @var mixed[]
     */
    protected $binds = array();

    /**
     * @var \Rougin\Ezekiel\DialectInterface|null
     */
    protected $dialect = null;

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
     * @var \Rougin\Ezekiel\Query\Update
     */
    protected $update;

    /**
     * Converts snake_case methods to camelCase.
     *
     * @param string  $method
     * @param mixed[] $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $parts = str_replace('_', ' ', $method);

        $camel = str_replace(' ', '', ucwords($parts));

        $camel = lcfirst($camel);

        if (method_exists($this, $camel))
        {
            /** @var callable */
            $callback = array($this, $camel);

            return call_user_func_array($callback, $args);
        }

        $error = __CLASS__ . '::' . $method . '()';

        throw new \BadMethodCallException($error);
    }

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
     * @return \Rougin\Ezekiel\Query\Having
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
     * @return \Rougin\Ezekiel\Query\Order
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
     * @return \Rougin\Ezekiel\Query\Where
     */
    public function andWhere($key)
    {
        return new Where($this, $key, Where::GROUP_AND);
    }

    /**
     * Generates a grouped "AND WHERE" query using a callable.
     *
     * @param callable $callback
     *
     * @return self
     */
    public function andWhereGroup($callback)
    {
        return $this->addWhereGroup($callback, Compare::GROUP_AND);
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
     * @return mixed[]
     */
    public function getBinds()
    {
        return $this->binds;
    }

    /**
     * Returns the current dialect.
     *
     * @return \Rougin\Ezekiel\DialectInterface
     */
    public function getDialect()
    {
        if (! $this->dialect)
        {
            return new MysqlDialect;
        }

        return $this->dialect;
    }

    /**
     * Returns all registered query items.
     *
     * @return \Rougin\Ezekiel\QueryInterface[]
     */
    public function getItems()
    {
        return $this->items;
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
     * @return \Rougin\Ezekiel\Query\Having
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
     * @return \Rougin\Ezekiel\Query\Join
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
     * @return \Rougin\Ezekiel\Query\Insert
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
     * @return \Rougin\Ezekiel\Query\Join
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
     * Merges two bind arrays, grouping values for duplicate keys into an array.
     *
     * @param mixed[] $a
     * @param mixed[] $b
     *
     * @return mixed[]
     */
    public function mergeBinds(array $a, array $b)
    {
        foreach ($b as $key => $value)
        {
            if (! array_key_exists($key, $a))
            {
                $a[$key] = $value;

                continue;
            }

            $existing = $a[$key];

            if (! is_array($existing))
            {
                $existing = array($existing);
            }

            $incoming = is_array($value) ? $value : array($value);

            $a[$key] = array_merge($existing, $incoming);
        }

        return $a;
    }

    /**
     * Generates an "OR HAVING" query.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Query\Having
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
     * @return \Rougin\Ezekiel\Query\Where
     */
    public function orWhere($key)
    {
        return new Where($this, $key, Where::GROUP_OR);
    }

    /**
     * Generates a grouped "OR WHERE" query using a callable.
     *
     * @param callable $callback
     *
     * @return self
     */
    public function orWhereGroup($callback)
    {
        return $this->addWhereGroup($callback, Compare::GROUP_OR);
    }

    /**
     * Generates an "ORDER BY" query.
     *
     * @param string $key
     *
     * @return \Rougin\Ezekiel\Query\Order
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
     * @return \Rougin\Ezekiel\Query\Join
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
     * @return \Rougin\Ezekiel\Query\Select
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
     * Sets the dialect to use for SQL generation.
     *
     * @param \Rougin\Ezekiel\DialectInterface $dialect
     *
     * @return self
     */
    public function setDialect(DialectInterface $dialect)
    {
        $this->dialect = $dialect;

        return $this;
    }

    /**
     * Returns the safe and compiled SQL.
     *
     * @return string
     */
    public function toSql()
    {
        $dialect = $this->getDialect();

        $sql = $this->setSelectSql();

        if ($this->type === self::TYPE_INSERT)
        {
            $sql = $this->setInsertSql();
        }

        if ($this->type === self::TYPE_DELETE)
        {
            $sql = 'DELETE FROM ' . $dialect->quote($this->table);
        }

        if ($this->type === self::TYPE_UPDATE)
        {
            $sql = $this->update->toSql();

            $this->binds = $this->update->getValues();
        }

        $sql = $this->setJoinSql($sql);

        $sql = $this->setCompareSql($sql, self::TYPE_WHERE);

        if ($this->type === self::TYPE_GROUP)
        {
            $sql .= ' GROUP BY ' . implode(', ', $this->groups);
        }

        $sql = $this->setCompareSql($sql, self::TYPE_HAVING);

        $sql = $this->setOrderSql($sql);

        if ($this->limit > 0)
        {
            $sql .= $dialect->toLimit($this->limit, $this->offset);
        }

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
     * @return \Rougin\Ezekiel\Query\Where
     */
    public function where($key)
    {
        return new Where($this, $key);
    }

    /**
     * Generates a grouped "WHERE" query using a callable.
     *
     * @param callable $callback
     *
     * @return self
     */
    public function whereGroup($callback)
    {
        return $this->addWhereGroup($callback, Compare::GROUP_NONE);
    }

    /**
     * @param callable $callback
     * @param integer  $group
     *
     * @return self
     */
    protected function addWhereGroup($callback, $group)
    {
        $self = new Query;

        call_user_func($callback, $self);

        $item = new WhereGroup($self, $group);

        return $this->addItem($item);
    }

    /**
     * @param string  $sql
     * @param integer $type
     * @param string  $prefix
     * @param string  $separator
     *
     * @return string
     */
    protected function getItemList($sql, $type, $prefix, $separator)
    {
        $first = true;

        $items = array();

        foreach ($this->items as $item)
        {
            if ($item->getType() !== $type)
            {
                continue;
            }

            $temp = $item->toSql();

            if (! $first)
            {
                $temp = str_replace($prefix, '', $temp);
            }

            $items[] = trim($temp);

            $first = false;
        }

        return trim($sql . ' ' . implode($separator, $items));
    }

    /**
     * @param string  $sql
     * @param integer $type
     *
     * @return string
     */
    protected function setCompareSql($sql, $type)
    {
        $prefix = $type === self::TYPE_HAVING ? 'HAVING' : 'WHERE';

        $first = true;

        $items = array();

        foreach ($this->items as $item)
        {
            if ($item->getType() !== $type)
            {
                continue;
            }

            if ($item instanceof Compare || $item instanceof WhereGroup)
            {
                $values = $item->getValues();

                $this->binds = $this->mergeBinds($this->binds, $values);
            }

            $temp = $item->toSql();

            if (! $first)
            {
                $temp = str_replace($prefix . ' ', '', $temp);
            }

            $temp = trim($temp);

            $matched = preg_match('/^(AND|OR)\s/i', $temp);

            if (! $first && $temp !== '' && ! $matched)
            {
                $temp = 'AND ' . $temp;
            }

            $items[] = $temp;

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
        return $this->getItemList($sql, self::TYPE_ORDER, 'ORDER BY', ', ');
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

            /** @var \Rougin\Ezekiel\Query\Select */
            $select = $item;

            $binds = $select->getSubqueryBinds();

            $this->binds = array_merge($this->binds, $binds);
        }

        return $sql;
    }
}

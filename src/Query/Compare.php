<?php

namespace Rougin\Ezekiel\Query;

use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\QueryInterface;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Compare implements QueryInterface
{
    const GROUP_AND = 1;

    const GROUP_NONE = 0;

    const GROUP_OR = 2;

    /**
     * @var integer
     */
    protected $group;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var mixed[]
     */
    protected $values = array();

    /**
     * @param integer $group
     * @param string  $default
     *
     * @return string
     */
    public static function groupToStr($group, $default = '')
    {
        if ($group === self::GROUP_AND)
        {
            return 'AND ';
        }

        if ($group === self::GROUP_OR)
        {
            return 'OR ';
        }

        return $default;
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param string                $key
     * @param integer               $group
     */
    public function __construct(Query $query, $key, $group = self::GROUP_NONE)
    {
        $this->query = $query;

        $this->key = $key;

        $this->group = $group;
    }

    /**
     * Generates a BETWEEN query comparison.
     *
     * @param mixed $min
     * @param mixed $max
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function between($min, $max)
    {
        $this->sql = $this->setSql($this->key, 'BETWEEN', '? AND ?');

        $this->values[$this->key] = array($min, $max);

        return $this->query->addItem($this);
    }

    /**
     * Generates an equality comparison.
     *
     * @param mixed $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function equals($value)
    {
        return $this->compareWith('=', $value);
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Generates a greater-than comparison.
     *
     * @param mixed $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function greaterThan($value)
    {
        return $this->compareWith('>', $value);
    }

    /**
     * Generates a greater-than or an equality comparison.
     *
     * @param mixed $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function greaterThanOrEqualTo($value)
    {
        return $this->compareWith('>=', $value);
    }

    /**
     * Generates an "IN ()" query comparison.
     *
     * @param \Rougin\Ezekiel\Query|mixed[] $values
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function in($values)
    {
        if ($values instanceof Query)
        {
            return $this->compareSubquery($values, 'IN');
        }

        return $this->compareIn($values, 'IN');
    }

    /**
     * Generates a false comparison.
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function isFalse()
    {
        return $this->compareBool(false);
    }

    /**
     * Generates an "IS NOT NULL" query comparison.
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function isNotNull()
    {
        return $this->compareNull('IS NOT');
    }

    /**
     * Generates an "IS NULL" query comparison.
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function isNull()
    {
        return $this->compareNull('IS');
    }

    /**
     * Generates a true comparison.
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function isTrue()
    {
        return $this->compareBool(true);
    }

    /**
     * Generates a less-than comparison.
     *
     * @param mixed $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function lessThan($value)
    {
        return $this->compareWith('<', $value);
    }

    /**
     * Generates a less-than or an equality comparison.
     *
     * @param mixed $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function lessThanOrEqualTo($value)
    {
        return $this->compareWith('<=', $value);
    }

    /**
     * Generates a LIKE query comparison.
     *
     * @param string $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function like($value)
    {
        return $this->compareWith('LIKE', $value);
    }

    /**
     * Generates a NOT BETWEEN query comparison.
     *
     * @param mixed $min
     * @param mixed $max
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function notBetween($min, $max)
    {
        $this->sql = $this->setSql($this->key, 'NOT BETWEEN', '? AND ?');

        $this->values[$this->key] = array($min, $max);

        return $this->query->addItem($this);
    }

    /**
     * Generates an non-equality comparison.
     *
     * @param mixed $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function notEqualTo($value)
    {
        return $this->compareWith('!=', $value);
    }

    /**
     * Generates an "NOT IN ()" query comparison.
     *
     * @param \Rougin\Ezekiel\Query|mixed[] $values
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function notIn($values)
    {
        if ($values instanceof Query)
        {
            return $this->compareSubquery($values, 'NOT IN');
        }

        return $this->compareIn($values, 'NOT IN');
    }

    /**
     * Generates a NOT LIKE query comparison.
     *
     * @param string $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function notLike($value)
    {
        return $this->compareWith('NOT LIKE', $value);
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return $this->sql;
    }

    /**
     * @param boolean $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    protected function compareBool($value)
    {
        $this->sql = $this->setSql($this->key, '=');

        $this->values[$this->key] = $value;

        return $this->query->addItem($this);
    }

    /**
     * @param mixed[] $values
     * @param string  $symbol
     *
     * @return \Rougin\Ezekiel\Query
     */
    protected function compareIn($values, $symbol)
    {
        $items = array_fill(0, count($values), '?');

        $value = '(' . implode(', ', $items) . ')';

        $this->sql = $this->setSql($this->key, $symbol, $value);

        $this->values[$this->key] = $values;

        return $this->query->addItem($this);
    }

    /**
     * @param string $symbol
     *
     * @return \Rougin\Ezekiel\Query
     */
    protected function compareNull($symbol)
    {
        $this->sql = $this->setSql($this->key, $symbol, 'NULL');

        return $this->query->addItem($this);
    }

    /**
     * @param \Rougin\Ezekiel\Query $sub
     * @param string                $symbol
     *
     * @return \Rougin\Ezekiel\Query
     */
    protected function compareSubquery(Query $sub, $symbol)
    {
        $subSql = '(' . $sub->toSql() . ')';

        $this->sql = $this->setSql($this->key, $symbol, $subSql);

        $subBinds = $sub->getBinds();

        $this->values = $this->query->mergeBinds($this->values, $subBinds);

        return $this->query->addItem($this);
    }

    /**
     * @param string $symbol
     * @param mixed  $value
     *
     * @return \Rougin\Ezekiel\Query
     */
    protected function compareWith($symbol, $value)
    {
        if (! $value instanceof Query)
        {
            $this->sql = $this->setSql($this->key, $symbol);

            $this->values[$this->key] = $value;

            return $this->query->addItem($this);
        }

        $sql = '(' . $value->toSql() . ')';

        $this->sql = $this->setSql($this->key, $symbol, $sql);

        $binds = $value->getBinds();

        $this->values = array_merge($this->values, $binds);

        return $this->query->addItem($this);
    }

    /**
     * @param string $key
     * @param string $symbol
     * @param string $value
     *
     * @return string
     */
    protected function setSql($key, $symbol, $value = '?')
    {
        $dialect = $this->query->getDialect();

        $group = self::groupToStr($this->group, '');

        $type = 'WHERE';

        if ($this->type === Query::TYPE_HAVING)
        {
            $type = 'HAVING';
        }

        $key = $dialect->quote($key);

        $sql = $type . ' ' . $group . $key . ' ';

        return $sql . $symbol . ' ' . $value;
    }
}

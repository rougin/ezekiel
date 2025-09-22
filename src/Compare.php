<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Compare implements QueryInterface
{
    const GROUP_AND = 0;

    const GROUP_OR = 1;

    /**
     * @var integer|null
     */
    protected $group = null;

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
    protected $values;

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param string                $key
     */
    public function __construct(Query $query, $key)
    {
        $this->query = $query;

        $this->key = $key;
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
        $this->sql = $this->setSql($this->key, '=');

        $this->values = array($value);

        return $this->query->addItem($this);
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
        $this->sql = $this->setSql($this->key, '>');

        $this->values = array($value);

        return $this->query->addItem($this);
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
        $this->sql = $this->setSql($this->key, '>=');

        $this->values = array($value);

        return $this->query->addItem($this);
    }

    /**
     * Generates an "IN ()" query comparison.
     *
     * @param mixed[] $values
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function in($values)
    {
        $items = array_fill(0, count($values), '?');

        $value = '(' . implode(', ', $items) . ')';

        $this->sql = $this->setSql($this->key, 'IN', $value);

        $this->values = $values;

        return $this->query->addItem($this);
    }

    /**
     * Generates a false comparison.
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function isFalse()
    {
        return $this->equals('FALSE');
    }

    /**
     * Generates an "IS NOT NULL" query comparison.
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function isNotNull()
    {
        $this->sql = $this->setSql($this->key, 'IS NOT', 'NULL');

        return $this->query->addItem($this);
    }

    /**
     * Generates an "IS NULL" query comparison.
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function isNull()
    {
        $this->sql = $this->setSql($this->key, 'IS', 'NULL');

        return $this->query->addItem($this);
    }

    /**
     * Generates a true comparison.
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function isTrue()
    {
        return $this->equals('TRUE');
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
        $this->sql = $this->setSql($this->key, '<');

        $this->values = array($value);

        return $this->query->addItem($this);
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
        $this->sql = $this->setSql($this->key, '<=');

        $this->values = array($value);

        return $this->query->addItem($this);
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
        $this->sql = $this->setSql($this->key, 'LIKE');

        $this->values = array($value);

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
        $this->sql = $this->setSql($this->key, '!=');

        $this->values = array($value);

        return $this->query->addItem($this);
    }

    /**
     * Generates an "NOT IN ()" query comparison.
     *
     * @param mixed[] $values
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function notIn($values)
    {
        $items = array_fill(0, count($values), '?');

        $value = '(' . implode(', ', $items) . ')';

        $this->sql = $this->setSql($this->key, 'NOT IN', $value);

        $this->values = $values;

        return $this->query->addItem($this);
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
        $this->sql = $this->setSql($this->key, 'NOT LIKE');

        $this->values = array($value);

        return $this->query->addItem($this);
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return $this->sql;
    }

    /**
     * @return self
     */
    public function useAnd()
    {
        $this->group = self::GROUP_AND;

        return $this;
    }

    /**
     * @return self
     */
    public function useOr()
    {
        $this->group = self::GROUP_OR;

        return $this;
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
        $group = '';

        if ($this->group === self::GROUP_AND)
        {
            $group = 'AND ';
        }

        if ($this->group === self::GROUP_OR)
        {
            $group = 'OR ';
        }

        return $group . '"' . $key . '" ' . $symbol . ' ' . $value;
    }
}

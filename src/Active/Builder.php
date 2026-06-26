<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\DialectInterface;
use Rougin\Ezekiel\Query;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Builder
{
    /**
     * @var \Rougin\Ezekiel\DialectInterface
     */
    protected $dialect;

    /**
     * @var integer|null
     */
    protected $limit = null;

    /**
     * @var integer|null
     */
    protected $offset = null;

    /**
     * @var boolean
     */
    protected $softDeletes = false;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array<integer, array<string, mixed>>
     */
    protected $wheres = array();

    /**
     * @param \Rougin\Ezekiel\DialectInterface $dialect
     * @param string                           $table
     */
    public function __construct(DialectInterface $dialect, $table)
    {
        $this->table = $table;

        $this->dialect = $dialect;
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     *
     * @return void
     */
    public function filter(Query $query)
    {
        $first = true;

        if ($this->softDeletes)
        {
            $query->where('deleted_at')->isNull();

            $first = false;
        }

        foreach ($this->wheres as $where)
        {
            /** @var string */
            $type = $where['type'];

            /** @var string */
            $col = $where['column'];

            /** @var string */
            $comparison = $where['comparison'];

            /** @var mixed */
            $value = $where['value'];

            if ($type === 'OR' && ! $first)
            {
                $compare = $query->orWhere($col);
            }
            elseif (! $first)
            {
                $compare = $query->andWhere($col);
            }
            else
            {
                $compare = $query->where($col);
            }

            $compare->parse($comparison, $value);

            $first = false;
        }
    }

    /**
     * @param integer $value
     *
     * @return self
     */
    public function limit($value)
    {
        $this->limit = $value;

        return $this;
    }

    /**
     * @return \Rougin\Ezekiel\Query
     */
    public function newQuery()
    {
        $query = new Query;

        $query->setDialect($this->dialect);

        return $query;
    }

    /**
     * @param integer $value
     *
     * @return self
     */
    public function offset($value)
    {
        $this->offset = $value;

        return $this;
    }

    /**
     * @param string       $column
     * @param mixed|string $value
     *
     * @return self
     */
    public function orWhere($column, $value)
    {
        $item = array('type' => 'OR', 'column' => $column);

        if (func_num_args() > 2)
        {
            $item['comparison'] = $value;

            $item['value'] = func_get_arg(2);
        }
        else
        {
            $item['comparison'] = '=';

            $item['value'] = $value;
        }

        $this->wheres[] = $item;

        return $this;
    }

    /**
     * @param string $column
     *
     * @return self
     */
    public function orWhereNotNull($column)
    {
        $this->wheres[] = array(
            'type' => 'OR',
            'column' => $column,
            'comparison' => 'is_not_null',
            'value' => null
        );

        return $this;
    }

    /**
     * @param string $column
     *
     * @return self
     */
    public function orWhereNull($column)
    {
        $this->wheres[] = array(
            'type' => 'OR',
            'column' => $column,
            'comparison' => 'is_null',
            'value' => null
        );

        return $this;
    }

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @return self
     */
    public function orWhereLike($column, $value)
    {
        $this->wheres[] = array(
            'type' => 'OR',
            'column' => $column,
            'comparison' => 'like',
            'value' => $value
        );

        return $this;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->limit = null;

        $this->wheres = array();

        $this->offset = null;
    }

    /**
     * @return \Rougin\Ezekiel\Query
     */
    public function toCountQuery()
    {
        $query = $this->newQuery();

        $select = array('COUNT(*)');

        $query->select($select)->from($this->table);

        $this->filter($query);

        return $query;
    }

    /**
     * @return \Rougin\Ezekiel\Query
     */
    public function toQuery()
    {
        $query = $this->newQuery();

        $query->select('*')->from($this->table);

        $this->filter($query);

        if ($this->limit === null)
        {
            return $query;
        }

        $limit = $this->limit;

        $offset = 0;

        if ($this->offset !== null)
        {
            $offset = $this->offset;
        }

        return $query->limit($limit, $offset);
    }

    /**
     * @param boolean $flag
     *
     * @return self
     */
    public function useSoftDeletes($flag = true)
    {
        $this->softDeletes = $flag;

        return $this;
    }

    /**
     * @param string       $column
     * @param mixed|string $value
     *
     * @return self
     */
    public function where($column, $value)
    {
        $item = array('type' => 'AND', 'column' => $column);

        if (func_num_args() > 2)
        {
            $item['comparison'] = $value;

            $item['value'] = func_get_arg(2);
        }
        else
        {
            $item['comparison'] = '=';

            $item['value'] = $value;
        }

        $this->wheres[] = $item;

        return $this;
    }

    /**
     * @param string  $column
     * @param mixed[] $values
     *
     * @return self
     */
    public function whereIn($column, $values)
    {
        $this->wheres[] = array(
            'type' => 'AND',
            'column' => $column,
            'comparison' => 'in',
            'value' => $values
        );

        return $this;
    }

    /**
     * @param string $column
     *
     * @return self
     */
    public function whereNotNull($column)
    {
        $this->wheres[] = array(
            'type' => 'AND',
            'column' => $column,
            'comparison' => 'is_not_null',
            'value' => null
        );

        return $this;
    }

    /**
     * @param string $column
     *
     * @return self
     */
    public function whereNull($column)
    {
        $this->wheres[] = array(
            'type' => 'AND',
            'column' => $column,
            'comparison' => 'is_null',
            'value' => null
        );

        return $this;
    }

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @return self
     */
    public function whereLike($column, $value)
    {
        $this->wheres[] = array(
            'type' => 'AND',
            'column' => $column,
            'comparison' => 'like',
            'value' => $value
        );

        return $this;
    }
}

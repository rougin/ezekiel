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
    public function filter($query)
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

            $method = 'where';

            if (! $first)
            {
                $method = 'and_where';
            }

            if ($type === 'OR')
            {
                $method = 'or_where';
            }

            /** @var string */
            $col = $where['column'];

            // [TODO] Improve logic ------------------------------------------
            $exists = isset($where['comparison']);

            $comparison = $exists ? $where['comparison'] : $where['operator'];
            // ---------------------------------------------------------------

            /** @var mixed */
            $value = $where['value'];

            switch ($comparison)
            {
                case '=':
                    $query->$method($col)->equals($value);

                    break;
                case 'like':
                case 'LIKE':
                    /** @var string $value */
                    $query->$method($col)->like($value);

                    break;
                case 'in':
                case 'IN':
                    /** @var mixed[] $value */
                    $query->$method($col)->in($value);

                    break;
                case '!=':
                    $query->$method($col)->notEqualTo($value);

                    break;
                case '>':
                    $query->$method($col)->greaterThan($value);

                    break;
                case '<':
                    $query->$method($col)->lessThan($value);

                    break;
                case '>=':
                    $query->$method($col)->greaterThanOrEqualTo($value);

                    break;
                case '<=':
                    $query->$method($col)->lessThanOrEqualTo($value);

                    break;
                default:
                    $query->$method($col)->equals($value);

                    break;
            }

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
     * @param string $column
     * @param string $operator
     * @param mixed  $value
     *
     * @return self
     */
    public function orWhere($column, $operator, $value)
    {
        $this->wheres[] = array(
            'type' => 'OR',
            'column' => $column,
            'operator' => $operator,
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
     * @param string $column
     * @param mixed  $value
     *
     * @return self
     */
    public function where($column, $value)
    {
        $item = array('type' => 'AND');

        $item['column'] = $column;

        $item['operator'] = '=';

        $item['value'] = $value;

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
        $item = array('type' => 'AND');

        $item['column'] = $column;

        $item['operator'] = '=';

        $item['comparison'] = 'in';

        $item['value'] = $values;

        $this->wheres[] = $item;

        return $this;
    }
}

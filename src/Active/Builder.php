<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\Result;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Builder
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string[]
     */
    protected $columns = array('*');

    /**
     * @var boolean
     */
    protected $distinct = false;

    /**
     * @var string[]
     */
    protected $eagers = array();

    /**
     * @var string[]
     */
    protected $groups = array();

    /**
     * @var integer
     */
    protected $limit = 0;

    /**
     * @var integer
     */
    protected $offset = 0;

    /**
     * @var array<integer, array<string, string>>
     */
    protected $orders = array();

    /**
     * @var \PDO|null
     */
    protected $pdo = null;

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var array<integer, array<string, mixed>>
     */
    protected $wheres = array();

    /**
     * @param string    $class
     * @param \PDO|null $pdo
     * @param string    $table
     */
    public function __construct($class, \PDO $pdo = null, $table = '')
    {
        $this->class = $class;

        $this->pdo = $pdo;

        $this->table = $table;
    }

    /**
     * @return integer
     */
    public function count()
    {
        $model = $this->getModel();

        $query = new Query;

        $query->select('COUNT(*) AS aggregate')
            ->from($model->getTable());

        $this->applyWheresTo($query);

        /** @var \PDO $pdo */
        $pdo = $this->pdo;

        $result = new Result($pdo);

        /** @var array<string, mixed>|boolean $row */
        $row = $result->first($query);

        if (is_array($row) && isset($row['aggregate']))
        {
            $val = $row['aggregate'];

            if (is_numeric($val))
            {
                return (int) $val;
            }
        }

        return 0;
    }

    /**
     * @param array<string, mixed> $attrs
     *
     * @return \Rougin\Ezekiel\Active\Model
     */
    public function create(array $attrs = array())
    {
        $class = $this->class;

        /** @var \Rougin\Ezekiel\Active\Model $model */
        $model = new $class;

        if ($this->pdo)
        {
            $model->setPdo($this->pdo);
        }

        if ($this->table)
        {
            $model->setTable($this->table);
        }

        $model->fill($attrs);

        $model->save();

        return $model;
    }

    /**
     * @return integer
     */
    public function delete()
    {
        $model = $this->getModel();

        $query = new Query;

        $query->deleteFrom($model->getTable());

        $this->applyWheresTo($query);

        $sql = $query->toSql();

        $binds = $this->flattenBinds($query->getBinds());

        /** @var \PDO $execPdo */
        $execPdo = $this->pdo;

        $stmt = $execPdo->prepare($sql);

        $stmt->execute($binds);

        return $stmt->rowCount();
    }

    /**
     * @return $this
     */
    public function distinct()
    {
        $this->distinct = true;

        return $this;
    }

    /**
     * @param mixed           $id
     * @param string|string[] $columns
     *
     * @return \Rougin\Ezekiel\Active\Model|null
     */
    public function find($id, $columns = array('*'))
    {
        return $this->where($this->getModel()
            ->getKeyName(), '=', $id)->first($columns);
    }

    /**
     * @param mixed           $id
     * @param string|string[] $columns
     *
     * @return \Rougin\Ezekiel\Active\Model
     * @throws \RuntimeException
     */
    public function findOrFail($id, $columns = array('*'))
    {
        $result = $this->find($id, $columns);

        if (is_null($result))
        {
            $class = $this->class;

            $idStr = is_scalar($id) ? (string) $id : '?';

            throw new \RuntimeException("No query results for model [$class] with key [$idStr].");
        }

        return $result;
    }

    /**
     * @param string|string[] $columns
     *
     * @return \Rougin\Ezekiel\Active\Model|null
     */
    public function first($columns = array('*'))
    {
        $this->limit(1);

        $items = $this->get($columns);

        if (empty($items))
        {
            return null;
        }

        return reset($items);
    }

    /**
     * @param string|string[] $columns
     *
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    public function get($columns = array('*'))
    {
        if (func_num_args() > 0)
        {
            $this->columns = is_string($columns) ? array($columns) : $columns;
        }

        $query = $this->buildSelectQuery();

        /** @var \PDO $execPdo */
        $execPdo = $this->pdo;

        $result = new Result($execPdo);

        /** @var array<string, mixed>[] $rows */
        $rows = $result->items($query);

        $models = $this->hydrateModels($rows);

        if (! empty($this->eagers))
        {
            $models = $this->eagerLoadRelations($models);
        }

        return $models;
    }

    /**
     * @return string[]
     */
    public function getEagers()
    {
        return $this->eagers;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model
     */
    public function getModel()
    {
        $class = $this->class;

        /** @var \Rougin\Ezekiel\Active\Model $model */
        $model = new $class;

        if ($this->table)
        {
            $model->setTable($this->table);
        }

        if ($this->pdo)
        {
            $model->setPdo($this->pdo);
        }

        return $model;
    }

    /**
     * @param string|string[] $groups
     *
     * @return $this
     */
    public function groupBy($groups)
    {
        if (is_string($groups))
        {
            $groups = array($groups);
        }

        $this->groups = $groups;

        return $this;
    }

    /**
     * @param integer $value
     *
     * @return $this
     */
    public function limit($value)
    {
        $this->limit = $value;

        return $this;
    }

    /**
     * @param integer $value
     *
     * @return $this
     */
    public function offset($value)
    {
        $this->offset = $value;

        return $this;
    }

    /**
     * @param callable|string $column
     * @param string|null     $operator
     * @param mixed|null      $value
     *
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orders[] = array(
            'column' => $column,
            'direction' => $direction,
        );

        return $this;
    }

    /**
     * @param string|string[] $columns
     *
     * @return $this
     */
    public function select($columns = array('*'))
    {
        if (is_string($columns))
        {
            $columns = array($columns);
        }

        $this->columns = $columns;

        return $this;
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return integer
     */
    public function update(array $values)
    {
        $query = new Query;

        $query->update($this->getModel()->getTable());

        foreach ($values as $key => $value)
        {
            $query->set($key, $value);
        }

        $this->applyWheresTo($query);

        $sql = $query->toSql();

        $binds = $this->flattenBinds($query->getBinds());

        /** @var \PDO $execPdo */
        $execPdo = $this->pdo;

        $stmt = $execPdo->prepare($sql);

        $stmt->execute($binds);

        return $stmt->rowCount();
    }

    /**
     * @param callable|string|string[] $column
     * @param string|null              $operator
     * @param mixed|null               $value
     * @param string                   $boolean
     *
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_callable($column))
        {
            $query = new self($this->class, $this->pdo, $this->table);

            call_user_func($column, $query);

            $this->wheres[] = array(
                'type' => 'nested',
                'query' => $query,
                'boolean' => $boolean,
            );

            return $this;
        }

        if (func_num_args() === 2)
        {
            $value = $operator;

            $operator = '=';
        }

        if (is_null($operator))
        {
            $operator = '=';
        }

        $this->wheres[] = array(
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        );

        return $this;
    }

    /**
     * @param string  $column
     * @param mixed[] $values
     * @param string  $boolean
     * @param boolean $not
     *
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'not_in' : 'in';

        $this->wheres[] = array(
            'type' => $type,
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean,
        );

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereNotNull($column)
    {
        return $this->whereNull($column, 'and', true);
    }

    /**
     * @param string  $column
     * @param string  $boolean
     * @param boolean $not
     *
     * @return $this
     */
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        $type = $not ? 'not_null' : 'null';

        $this->wheres[] = array(
            'type' => $type,
            'column' => $column,
            'boolean' => $boolean,
        );

        return $this;
    }

    /**
     * @param string|string[] $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        if (is_string($relations))
        {
            $relations = explode(',', $relations);
        }

        foreach ($relations as $name)
        {
            $name = trim($name);

            if (! in_array($name, $this->eagers, true))
            {
                $this->eagers[] = $name;
            }
        }

        return $this;
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param array<string, mixed>  $where
     * @param string                $boolean
     * @param integer               $index
     *
     * @return void
     */
    protected function applyBasicWhere(Query $query, array $where, $boolean, $index)
    {
        /** @var string $column */
        $column = $where['column'];

        $operator = $where['operator'];

        $value = $where['value'];

        if ($index === 0)
        {
            $comparison = $query->where($column);
        }

        if ($index !== 0 && $boolean === 'or')
        {
            $comparison = $query->orWhere($column);
        }

        if ($index !== 0 && $boolean !== 'or')
        {
            $comparison = $query->andWhere($column);
        }

        if (! isset($comparison))
        {
            return;
        }

        if ($operator === '=')
        {
            $comparison->equals($value);
        }

        if ($operator === '!=' || $operator === '<>')
        {
            $comparison->notEqualTo($value);
        }

        if ($operator === '>')
        {
            $comparison->greaterThan($value);
        }

        if ($operator === '>=')
        {
            $comparison->greaterThanOrEqualTo($value);
        }

        if ($operator === '<')
        {
            $comparison->lessThan($value);
        }

        if ($operator === '<=')
        {
            $comparison->lessThanOrEqualTo($value);
        }

        if ($operator === 'like' || $operator === 'LIKE')
        {
            /** @var string $value */
            $comparison->like($value);
        }

        if ($operator === 'not like' || $operator === 'NOT LIKE')
        {
            /** @var string $value */
            $comparison->notLike($value);
        }
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param array<string, mixed>  $where
     * @param string                $boolean
     * @param boolean               $not
     * @param integer               $index
     *
     * @return void
     */
    protected function applyInWhere(Query $query, array $where, $boolean, $not, $index)
    {
        /** @var string $column */
        $column = $where['column'];

        /** @var mixed[] */
        $values = $where['values'];

        if ($index === 0)
        {
            $comparison = $query->where($column);
        }
        elseif ($boolean === 'or')
        {
            $comparison = $query->orWhere($column);
        }
        else
        {
            $comparison = $query->andWhere($column);
        }

        if ($not)
        {
            $comparison->notIn($values);
        }
        else
        {
            $comparison->in($values);
        }
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param array<string, mixed>  $where
     * @param string                $boolean
     * @param boolean               $not
     * @param integer               $index
     *
     * @return void
     */
    protected function applyNullWhere(Query $query, array $where, $boolean, $not, $index)
    {
        /** @var string $column */
        $column = $where['column'];

        if ($index === 0)
        {
            $comparison = $query->where($column);
        }
        elseif ($boolean === 'or')
        {
            $comparison = $query->orWhere($column);
        }
        else
        {
            $comparison = $query->andWhere($column);
        }

        if ($not)
        {
            $comparison->isNotNull();
        }
        else
        {
            $comparison->isNull();
        }
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     *
     * @return void
     */
    protected function applyOrdersTo(Query $query)
    {
        $first = true;

        foreach ($this->orders as $order)
        {
            $column = $order['column'];

            $direction = $order['direction'];

            if ($first)
            {
                $orderBy = $query->orderBy($column);
            }
            else
            {
                $orderBy = $query->andOrderBy($column);
            }

            if (strtolower($direction) === 'desc')
            {
                $orderBy->desc();
            }
            else
            {
                $orderBy->asc();
            }

            $first = false;
        }
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     *
     * @return void
     */
    protected function applyWheresTo(Query $query)
    {
        foreach ($this->wheres as $index => $where)
        {
            $type = isset($where['type']) ? $where['type'] : null;

            /** @var string $boolean */
            $boolean = isset($where['boolean']) ? $where['boolean'] : 'and';

            if ($type === 'basic')
            {
                $this->applyBasicWhere($query, $where, $boolean, $index);
            }

            if ($type === 'in')
            {
                $this->applyInWhere($query, $where, $boolean, false, $index);
            }

            if ($type === 'not_in')
            {
                $this->applyInWhere($query, $where, $boolean, true, $index);
            }

            if ($type === 'null')
            {
                $this->applyNullWhere($query, $where, $boolean, false, $index);
            }

            if ($type === 'not_null')
            {
                $this->applyNullWhere($query, $where, $boolean, true, $index);
            }
        }
    }

    /**
     * @return \Rougin\Ezekiel\Query
     */
    protected function buildSelectQuery()
    {
        $model = $this->getModel();

        $table = $model->getTable();

        $columns = $this->columns;

        if (in_array('*', $columns, true))
        {
            $columns = array($table . '.*');
        }

        $query = new Query;

        $select = $query->select(implode(', ', $columns));

        if ($this->distinct)
        {
            $select->distinct();
        }

        $select->from($table);

        $this->applyWheresTo($query);

        if (! empty($this->groups))
        {
            $query->groupBy($this->groups);
        }

        if (! empty($this->orders))
        {
            $this->applyOrdersTo($query);
        }

        if ($this->limit > 0)
        {
            $query->limit($this->limit, $this->offset);
        }

        return $query;
    }

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     * @param string                         $name
     *
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    protected function eagerLoadRelation(array $models, $name)
    {
        if (empty($models))
        {
            return $models;
        }

        $first = reset($models);

        if (! method_exists($first, $name))
        {
            return $models;
        }

        /** @var callable $callback */
        $callback = array($first, $name);

        /** @var \Rougin\Ezekiel\Active\Relations\Relation $relation */
        $relation = call_user_func($callback);

        $relation->addEagers($models);

        $results = $relation->getEager();

        $relation->match($models, $results, $name);

        return $models;
    }

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     *
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    protected function eagerLoadRelations(array $models)
    {
        foreach ($this->eagers as $name)
        {
            $models = $this->eagerLoadRelation($models, $name);
        }

        return $models;
    }

    /**
     * @param mixed[] $binds
     *
     * @return mixed[]
     */
    protected function flattenBinds(array $binds)
    {
        $flat = array();

        foreach ($binds as $value)
        {
            if (! is_array($value))
            {
                $flat[] = $value;

                continue;
            }

            foreach ($value as $v)
            {
                $flat[] = $v;
            }
        }

        return $flat;
    }

    /**
     * @param array<string, mixed>[] $rows
     *
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    protected function hydrateModels(array $rows)
    {
        $models = array();

        $class = $this->class;

        foreach ($rows as $row)
        {
            /** @var \Rougin\Ezekiel\Active\Model $model */
            $model = new $class;

            if ($this->pdo)
            {
                $model->setPdo($this->pdo);
            }

            if ($this->table)
            {
                $model->setTable($this->table);
            }

            $model->setRawAttributes($row, true);

            $models[] = $model;
        }

        return $models;
    }
}

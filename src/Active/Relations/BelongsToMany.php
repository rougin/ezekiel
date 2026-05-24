<?php

namespace Rougin\Ezekiel\Active\Relations;

use Rougin\Ezekiel\Active\Model;
use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\Result;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class BelongsToMany extends Relation
{
    /**
     * @var string
     */
    protected $foreignPivotKey;

    /**
     * @var string
     */
    protected $parentKey;

    /**
     * @var string[]
     */
    protected $pivotColumns = array();

    /**
     * @var string
     */
    protected $relatedKey;

    /**
     * @var string
     */
    protected $relatedPivotKey;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var boolean
     */
    protected $withTimestamps = false;

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $related
     * @param string                       $table
     * @param string                       $foreignPivotKey
     * @param string                       $relatedPivotKey
     * @param string                       $parentKey
     * @param string                       $relatedKey
     */
    public function __construct(
        Model $parent,
        $related,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey
    ) {
        parent::__construct($parent, $related);

        $this->table = $table;

        $this->foreignPivotKey = $foreignPivotKey;

        $this->relatedPivotKey = $relatedPivotKey;

        $this->parentKey = $parentKey;

        $this->relatedKey = $relatedKey;
    }

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     *
     * @return void
     */
    public function addEagers(array $models)
    {
        $keys = array();

        foreach ($models as $model)
        {
            $key = $model->getAttribute($this->parentKey);

            if ($key !== null)
            {
                $keys[] = $key;
            }
        }

        if (! empty($keys))
        {
            $this->query->whereIn(
                $this->table . '.' . $this->foreignPivotKey,
                $keys
            );
        }
    }

    /**
     * @param mixed                $id
     * @param array<string, mixed> $attrs
     *
     * @return void
     */
    public function attach($id, array $attrs = array())
    {
        $pivotData = array(
            $this->foreignPivotKey => $this->parent->getKey(),
            $this->relatedPivotKey => $id,
        );

        $pivotData = array_merge($pivotData, $attrs);

        if ($this->withTimestamps)
        {
            $now = date('Y-m-d H:i:s');

            $pivotData['created_at'] = $now;

            $pivotData['updated_at'] = $now;
        }

        $query = new Query;

        $query->insertInto($this->table)->values($pivotData);

        $sql = $query->toSql();

        $binds = array_values($query->getBinds());

        /** @var \PDO $attachPdo */
        $attachPdo = $this->parent->getPdo();

        $stmt = $attachPdo->prepare($sql);

        $stmt->execute($binds);
    }

    /**
     * @param mixed $id
     *
     * @return integer
     */
    public function detach($id = null)
    {
        $query = new Query;

        $query->deleteFrom($this->table);

        $query->where($this->foreignPivotKey)->equals($this->parent->getKey());

        if ($id !== null)
        {
            $query->andWhere($this->relatedPivotKey)->equals($id);
        }

        $sql = $query->toSql();

        $binds = array_values($query->getBinds());

        /** @var \PDO $detachPdo */
        $detachPdo = $this->parent->getPdo();

        $stmt = $detachPdo->prepare($sql);

        $stmt->execute($binds);

        return $stmt->rowCount();
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    public function getEager()
    {
        $this->query->whereIn(
            $this->table . '.' . $this->foreignPivotKey,
            array($this->parent->getAttribute($this->parentKey))
        );

        return $this->performJoin();
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    public function getResults()
    {
        if ($this->results === null)
        {
            $this->results = $this->performJoin();
        }

        /** @var \Rougin\Ezekiel\Active\Model[] $results */
        $results = $this->results;

        return $results;
    }

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     * @param mixed                          $results
     * @param string                         $relation
     *
     * @return void
     */
    public function match(array $models, $results, $relation)
    {
        $dictionary = array();

        /** @var \Rougin\Ezekiel\Active\Model[] $resultList */
        $resultList = is_array($results) ? $results : array();

        foreach ($resultList as $result)
        {
            $pivotKey = $result->getAttribute('_pivotForeignKey');

            /** @var array<int|string, \Rougin\Ezekiel\Active\Model[]> $dictionary */

            if (! isset($dictionary[$pivotKey]))
            {
                $dictionary[$pivotKey] = array();
            }

            $dictionary[$pivotKey][] = $result;
        }

        foreach ($models as $model)
        {
            $key = $model->getAttribute($this->parentKey);

            /** @var array<int|string, \Rougin\Ezekiel\Active\Model[]> $dictionary */

            $value = isset($dictionary[$key]) ? $dictionary[$key] : array();

            $model->setRelation($relation, $value);
        }
    }

    /**
     * @param array<string, mixed> $attrs
     *
     * @return void
     */
    public function sync(array $attrs)
    {
    }

    /**
     * @param string|string[] $columns
     *
     * @return $this
     */
    public function withPivot($columns)
    {
        if (is_string($columns))
        {
            $columns = array($columns);
        }

        $this->pivotColumns = array_merge($this->pivotColumns, $columns);

        return $this;
    }

    /**
     * @return $this
     */
    public function withTimestamps()
    {
        $this->withTimestamps = true;

        $this->pivotColumns = array_merge(
            $this->pivotColumns,
            array('created_at', 'updated_at')
        );

        return $this;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    protected function performJoin()
    {
        $related = $this->getRelated();

        $relatedTable = $related->getTable();

        $pivotDotCols = array();

        foreach ($this->pivotColumns as $col)
        {
            $pivotDotCols[] = $this->table . '.' . $col;
        }

        $pivotDotCols[] = $this->table . '.' . $this->foreignPivotKey;

        $columns = array($relatedTable . '.*');

        $columns = array_merge($columns, $pivotDotCols);

        $allCols = trim(implode(', ', $columns));

        $query = new Query;

        $query->select($allCols)
            ->from($relatedTable);

        $query->innerJoin($this->table)
            ->on(
                $this->table . '.' . $this->relatedPivotKey,
                $relatedTable . '.' . $this->relatedKey
            );

        if ($this->parent->getAttribute($this->parentKey))
        {
            $query->where($this->table . '.' . $this->foreignPivotKey)
                ->equals($this->parent->getAttribute($this->parentKey));
        }

        $pdo = $this->parent->getPdo();

        /** @var \PDO $pdo */
        $result = new Result($pdo);

        /** @var array<string, mixed>[] $rows */
        $rows = $result->items($query);

        $models = array();

        $class = $this->related;

        foreach ($rows as $row)
        {
            /** @var \Rougin\Ezekiel\Active\Model $model */
            $model = new $class;

            $pdo = $this->parent->getPdo();

            if ($pdo)
            {
                $model->setPdo($pdo);
            }

            $modelAttributes = array();

            $pivotAttributes = array();

            foreach ($row as $key => $value)
            {
                if ($key === $this->foreignPivotKey)
                {
                    $model->setAttribute('_pivotForeignKey', $value);

                    continue;
                }

                if (in_array($key, $this->pivotColumns, true))
                {
                    $pivotAttributes[$key] = $value;

                    continue;
                }

                $modelAttributes[$key] = $value;
            }

            $model->setRawAttributes($modelAttributes, true);

            if (! empty($pivotAttributes))
            {
                $pivot = new \stdClass;

                foreach ($pivotAttributes as $k => $v)
                {
                    $pivot->{$k} = $v;
                }

                $model->setAttribute('_pivot', $pivot);
            }

            $models[] = $model;
        }

        return $models;
    }
}

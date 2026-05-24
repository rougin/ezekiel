<?php

namespace Rougin\Ezekiel\Active\Relations;

use Rougin\Ezekiel\Active\Builder;
use Rougin\Ezekiel\Active\Depot;
use Rougin\Ezekiel\Active\Manager;
use Rougin\Ezekiel\Active\Model;
use Rougin\Ezekiel\Dialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class BelongsToMany
{
    /**
     * @var \Rougin\Ezekiel\Active\Depot
     */
    protected $depot;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var \Rougin\Ezekiel\Active\Model
     */
    protected $parent;

    /**
     * @var string[]
     */
    protected $pivots = array();

    /**
     * @var string
     */
    protected $related;

    /**
     * @var string
     */
    protected $relatedKey;

    /**
     * @var \Rougin\Ezekiel\Active\Model[]
     */
    protected $results = array();

    /**
     * @var string
     */
    protected $table;

    /**
     * @var boolean
     */
    protected $timestamps = false;

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $related
     * @param string|null                  $table
     * @param string|null                  $foreignKey
     * @param string|null                  $relatedKey
     */
    public function __construct(Model $parent, $related, $table = null, $foreignKey = null, $relatedKey = null)
    {
        /** @var \Rougin\Ezekiel\Active\Model */
        $instance = new $related;

        $this->foreignKey = $foreignKey ?: $parent->getForeignKey();

        $this->parent = $parent;

        $this->related = $related;

        $this->relatedKey = $relatedKey ?: $instance->getForeignKey();

        $this->table = $table ?: $parent->joiningTable($related);

        $name = $parent->getConnectionName();

        $this->depot = new Depot(new Manager, $name);
    }

    /**
     * @param integer              $id
     * @param array<string, mixed> $data
     *
     * @return string
     */
    public function attach($id, $data = array())
    {
        $key = $this->parent->getKey();

        $value = $this->parent->{$key};

        $attrs = array($this->foreignKey => $value);

        $attrs[$this->relatedKey] = $id;

        $attrs = array_merge($attrs, $data);

        if ($this->timestamps)
        {
            $now = date('Y-m-d H:i:s');

            $attrs['created_at'] = $now;

            $attrs['updated_at'] = $now;
        }

        return $this->depot->insert($this->table, $attrs);
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    public function getAll()
    {
        $key = $this->parent->getKey();

        $parentValue = $this->parent->{$key};

        // Set the dialect from PDO ------
        $pdo = $this->parent->getPdo();

        $dialect = Dialect::fromPdo($pdo);
        // -------------------------------

        $table = $this->table;

        $builder = new Builder($dialect, $table);

        $builder->where($this->foreignKey, $parentValue);

        /** @var array<integer, array<string, string>> */
        $rows = $this->depot->get($builder);

        $ids = array();

        $maps = array();

        foreach ($rows as $row)
        {
            /** @var string */
            $rid = $row[$this->relatedKey];

            $ids[] = $rid;

            $maps[$rid] = $row;
        }

        if (empty($ids))
        {
            return $this->results;
        }

        $related = $this->related;

        /** @var \Rougin\Ezekiel\Active\Model */
        $query = new $related;

        $models = $query->whereIn('id', $ids)->get();

        foreach ($models as $model)
        {
            $key = $model->getKey();

            $rid = $model->{$key};

            /** @var string $key */
            $key = $rid;

            /** @var array<string, string> */
            $data = $maps[$key];

            if (empty($this->pivots))
            {
                $model->setPivot((object) $data);

                continue;
            }

            $filtered = array();

            foreach ($this->pivots as $col)
            {
                if (array_key_exists($col, $data))
                {
                    $filtered[$col] = $data[$col];
                }
            }

            $data = $filtered;

            $model->setPivot((object) $data);
        }

        $this->results = $models;

        return $this->results;
    }

    /**
     * @param string|string[] $columns
     *
     * @return self
     */
    public function withPivot($columns)
    {
        if (! is_array($columns))
        {
            $columns = func_get_args();
        }

        /** @var string[] $columns */
        $this->pivots = $columns;

        return $this;
    }

    /**
     * @return self
     */
    public function withTimestamps()
    {
        $this->timestamps = true;

        return $this;
    }
}

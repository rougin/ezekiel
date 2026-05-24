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
    protected $foreign;

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
     * @param string|null                  $foreign
     * @param string|null                  $relatedKey
     */
    public function __construct(Model $parent, $related, $table = null, $foreign = null, $relatedKey = null)
    {
        /** @var \Rougin\Ezekiel\Active\Model */
        $instance = new $related;

        // Return foreign key from the parent model ---
        $default = $parent->getForeignKey();

        $this->foreign = $foreign ? $foreign : $default;
        // ---------------------------------------------

        $this->parent = $parent;

        $this->related = $related;

        // Return related key from the related model ---
        $default = $instance->getForeignKey();

        $this->relatedKey = $relatedKey;

        if (! $relatedKey)
        {
            $this->relatedKey = $default;
        }
        // ---------------------------------------------

        // Return joining table from the parent model ---
        $default = $parent->joiningTable($related);

        $this->table = $table ? $table : $default;
        // ----------------------------------------------

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
        $key = $this->parent->getPrimaryKey();

        $value = $this->parent->{$key};

        $attrs = array($this->foreign => $value);

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
        $key = $this->parent->getPrimaryKey();

        $parentValue = $this->parent->{$key};

        // Set the dialect from PDO ------
        $pdo = $this->parent->getPdo();

        $dialect = Dialect::fromPdo($pdo);
        // -------------------------------

        $table = $this->table;

        $builder = new Builder($dialect, $table);

        $builder->where($this->foreign, $parentValue);

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
            return array();
        }

        $related = $this->related;

        /** @var \Rougin\Ezekiel\Active\Model */
        $query = new $related;

        $models = $query->whereIn('id', $ids)->get();

        foreach ($models as $model)
        {
            $key = $model->getPrimaryKey();

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

        return $models;
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

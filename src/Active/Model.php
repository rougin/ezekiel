<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Active\Relations\BelongsTo;
use Rougin\Ezekiel\Active\Relations\BelongsToMany;
use Rougin\Ezekiel\Active\Relations\HasMany;
use Rougin\Ezekiel\Active\Relations\HasOne;
use Rougin\Ezekiel\Dialect;

/**
 * @property object|null $pivot
 *
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Model
{
    /**
     * @var array<string, mixed>
     */
    protected $attrs = array();

    /**
     * @var \Rougin\Ezekiel\Active\Builder|null
     */
    protected $builder = null;

    /**
     * @var array<string, string>
     */
    protected $casts = array();

    /**
     * @var string
     */
    protected $connection = 'default';

    /**
     * @var \Rougin\Ezekiel\Active\Depot|null
     */
    protected $depot = null;

    /**
     * @var string[]
     */
    protected $eagers = array();

    /**
     * @var boolean
     */
    protected $exists = false;

    /**
     * @var array<integer, string>
     */
    protected $fillable = array();

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array<string, mixed>
     */
    protected $relations = array();

    /**
     * @var boolean
     */
    protected $softDeletes = false;

    /**
     * @var string|null
     */
    protected $table = null;

    /**
     * @var boolean
     */
    protected $timestamps = true;

    /**
     * @param string $name
     * @param \PDO   $pdo
     *
     * @return void
     */
    public static function setPdo($name, \PDO $pdo)
    {
        Manager::set($name, $pdo);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        $method = $this->toAttr($key);

        if (method_exists($this, $method))
        {
            $value = null;

            if (array_key_exists($key, $this->attrs))
            {
                $value = $this->attrs[$key];
            }

            return $this->$method($value);
        }

        // Check cached relations first -------------
        if (array_key_exists($key, $this->relations))
        {
            return $this->relations[$key];
        }
        // ------------------------------------------

        if (method_exists($this, $key))
        {
            $relation = $this->$key();

            if ($relation instanceof BelongsToMany)
            {
                $items = $relation->getAll();

                $this->relations[$key] = $items;

                return $this->relations[$key];
            }

            if ($relation instanceof HasMany)
            {
                $items = $relation->getResults();

                $this->relations[$key] = $items;

                return $this->relations[$key];
            }

            if ($relation instanceof HasOne || $relation instanceof BelongsTo)
            {
                $items = $relation->getResults();

                $this->relations[$key] = $items;

                return $this->relations[$key];
            }

            return $relation;
        }

        if (array_key_exists($key, $this->attrs))
        {
            return $this->cast($key, $this->attrs[$key]);
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->attrs);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attrs[$key] = $value;
    }

    /**
     * Returns all models.
     *
     * @return static[]
     */
    public function all()
    {
        return $this->get();
    }

    /**
     * @param string      $class
     * @param string|null $foreign
     * @param string|null $owner
     *
     * @return \Rougin\Ezekiel\Active\Relations\BelongsTo
     */
    public function belongsTo($class, $foreign = null, $owner = null)
    {
        return new BelongsTo($this, $class, $foreign, $owner);
    }

    /**
     * @param string      $class
     * @param string|null $table
     * @param string|null $foreign
     * @param string|null $related
     *
     * @return \Rougin\Ezekiel\Active\Relations\BelongsToMany
     */
    public function belongsToMany($class, $table = null, $foreign = null, $related = null)
    {
        return new BelongsToMany($this, $class, $table, $foreign, $related);
    }

    /**
     * Returns the total record count.
     *
     * @return integer
     */
    public function count()
    {
        return $this->getDepot()->count($this->getBuilder());
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public function create($data)
    {
        $instance = $this->newInstance();

        $data = $instance->guard($data);

        if ($instance->timestamps)
        {
            $stamp = date('Y-m-d H:i:s');

            $data['created_at'] = $stamp;

            $data['updated_at'] = $stamp;
        }

        $table = $instance->getTable();

        $id = $this->getDepot()->insert($table, $data);

        $data[$instance->primaryKey] = $id;

        $instance->attrs = $data;

        $instance->exists = true;

        return $instance;
    }

    /**
     * @return boolean
     */
    public function delete()
    {
        $key = $this->primaryKey;

        $id = $this->attrs[$key];

        $table = $this->getTable();

        $depot = $this->getDepot();

        return $depot->deleteRow($table, $key, $id, $this->softDeletes);
    }

    /**
     * @return boolean
     */
    public function exists()
    {
        return $this->count() > 0;
    }

    /**
     * @param integer $id
     *
     * @return static|null
     */
    public function find($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * @param integer $id
     *
     * @return static
     * @throws \UnexpectedValueException
     */
    public function findOrFail($id)
    {
        return $this->where('id', $id)->firstOrFail();
    }

    /**
     * @return static|null
     */
    public function first()
    {
        $this->getBuilder()->limit(1);

        $results = $this->get();

        return $results ? $results[0] : null;
    }

    /**
     * @return static
     * @throws \UnexpectedValueException
     */
    public function firstOrFail()
    {
        $result = $this->first();

        if ($result === null)
        {
            $text = 'No record found';

            throw new \UnexpectedValueException($text);
        }

        return $result;
    }

    /**
     * @return static[]
     */
    public function get()
    {
        $builder = $this->getBuilder();

        $depot = $this->getDepot();

        $rows = $depot->get($builder);

        $results = $this->hydrate($rows);

        // Triggers "__get" on each model for each ---
        // eager-loaded relation. The relation is ----
        // lazily loaded once and cached in the ------
        // "$this->relations" of the same model. -----
        if (! empty($this->eagers))
        {
            foreach ($results as $model)
            {
                foreach ($this->eagers as $relation)
                {
                    $unused = $model->$relation;
                }
            }
        }
        // -------------------------------------------

        $this->reset();

        return $results;
    }

    /**
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        $class = get_class($this);

        $parts = explode('\\', $class);

        $base = end($parts);

        return strtolower($base) . '_id';
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        $manager = new Manager;

        return $manager->get($this->connection);
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        if ($this->table)
        {
            return $this->table;
        }

        // Guess table based on class name ------
        $parts = explode('\\', get_class($this));

        return strtolower(end($parts)) . 's';
        // --------------------------------------
    }

    /**
     * @param string      $class
     * @param string|null $foreign
     * @param string|null $local
     *
     * @return \Rougin\Ezekiel\Active\Relations\HasMany
     */
    public function hasMany($class, $foreign = null, $local = null)
    {
        return new HasMany($this, $class, $foreign, $local);
    }

    /**
     * @param string      $class
     * @param string|null $foreign
     * @param string|null $local
     *
     * @return \Rougin\Ezekiel\Active\Relations\HasOne
     */
    public function hasOne($class, $foreign = null, $local = null)
    {
        return new HasOne($this, $class, $foreign, $local);
    }

    /**
     * @param string $class
     *
     * @return string
     */
    public function joiningTable($class)
    {
        /** @var \Rougin\Ezekiel\Active\Model $instance */
        $instance = new $class;

        $segments = array($this->getTable(), $instance->getTable());

        sort($segments);

        return strtolower($segments[0] . '_' . $segments[1]);
    }

    /**
     * @param integer $value
     *
     * @return static
     */
    public function limit($value)
    {
        $this->getBuilder()->limit($value);

        return $this;
    }

    /**
     * @param integer $value
     *
     * @return static
     */
    public function offset($value)
    {
        $this->getBuilder()->offset($value);

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param mixed  $value
     *
     * @return static
     */
    public function orWhere($column, $operator, $value)
    {
        $builder = $this->getBuilder();

        $builder->orWhere($column, $operator, $value);

        return $this;
    }

    /**
     * @return boolean
     */
    public function save()
    {
        // [TODO] Make "updated_at" updatable ---
        $key = 'updated_at';
        // --------------------------------------

        if ($this->timestamps && ! array_key_exists($key, $this->attrs))
        {
            $this->attrs[$key] = date('Y-m-d H:i:s');
        }

        if (! $this->exists)
        {
            $instance = $this->create($this->attrs);

            $this->attrs = $instance->attrs;

            $this->exists = true;

            return true;
        }

        $data = $this->attrs;

        $pk = $this->primaryKey;

        $id = $data[$pk];

        unset($data[$pk]);

        $depot = $this->getDepot();

        $table = $this->getTable();

        $result = $depot->updateRow($table, $pk, $id, $data);

        $this->attrs[$pk] = $id;

        return $result;
    }

    /**
     * @param object $data
     *
     * @return void
     */
    public function setPivot($data)
    {
        $this->pivot = $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return $this->attrs;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return boolean
     */
    public function update($data)
    {
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }

        return $this->save();
    }

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @return static
     */
    public function where($column, $value)
    {
        $this->getBuilder()->where($column, $value);

        return $this;
    }

    /**
     * @param string  $column
     * @param mixed[] $values
     *
     * @return static
     */
    public function whereIn($column, $values)
    {
        $this->getBuilder()->whereIn($column, $values);

        return $this;
    }

    /**
     * @param string|string[] $relations
     *
     * @return static
     */
    public function with($relations)
    {
        if (is_string($relations))
        {
            $relations = array($relations);
        }

        $this->eagers = array_merge($this->eagers, $relations);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function cast($key, $value)
    {
        if ($value === null)
        {
            return null;
        }

        if (! isset($this->casts[$key]))
        {
            return $value;
        }

        $type = $this->casts[$key];

        if ($type === 'integer' || $type === 'int')
        {
            /** @phpstan-ignore-next-line */
            return (int) $value;
        }

        if ($type === 'boolean' || $type === 'bool')
        {
            return (bool) $value;
        }

        if ($type === 'float' || $type === 'double')
        {
            /** @phpstan-ignore-next-line */
            return (float) $value;
        }

        if ($type === 'string')
        {
            /** @phpstan-ignore-next-line */
            return (string) $value;
        }

        return $value;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Builder
     */
    protected function getBuilder()
    {
        if ($this->builder)
        {
            return $this->builder;
        }

        $table = $this->getTable();

        $pdo = $this->getPdo();

        $sql = Dialect::fromPdo($pdo);

        $this->builder = new Builder($sql, $table);

        if ($this->softDeletes)
        {
            $this->builder->useSoftDeletes();
        }

        return $this->builder;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Depot
     */
    protected function getDepot()
    {
        if ($this->depot)
        {
            return $this->depot;
        }

        $name = $this->connection;

        $manager = new Manager;

        $this->depot = new Depot($manager, $name);

        return $this->depot;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function guard($data)
    {
        $items = array();

        foreach ($data as $key => $value)
        {
            if (in_array($key, $this->fillable, true))
            {
                $items[$key] = $value;
            }
        }

        return $items;
    }

    /**
     * @param array<integer, array<string, mixed>> $rows
     *
     * @return static[]
     */
    protected function hydrate($rows)
    {
        $items = array();

        foreach ($rows as $row)
        {
            $items[] = $this->newInstance($row, true);
        }

        return $items;
    }

    /**
     * @param array<string, mixed> $attrs
     * @param boolean              $exists
     *
     * @return static
     */
    protected function newInstance($attrs = array(), $exists = false)
    {
        $class = get_class($this);

        /** @var static */
        $model = new $class;

        $model->attrs = $attrs;

        $model->casts = $this->casts;

        $model->connection = $this->connection;

        $model->exists = $exists;

        $model->fillable = $this->fillable;

        $model->softDeletes = $this->softDeletes;

        $model->table = $this->table;

        $model->timestamps = $this->timestamps;

        return $model;
    }

    /**
     * @return void
     */
    protected function reset()
    {
        $this->eagers = array();

        $this->getBuilder()->reset();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function toAttr($value)
    {
        $result = '';

        $parts = explode('_', $value);

        foreach ($parts as $part)
        {
            $result .= ucfirst(strtolower($part));
        }

        return 'get' . $result . 'Attribute';
    }
}

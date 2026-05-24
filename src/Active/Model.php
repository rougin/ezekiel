<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Active\Relations\BelongsTo;
use Rougin\Ezekiel\Active\Relations\BelongsToMany;
use Rougin\Ezekiel\Active\Relations\HasMany;
use Rougin\Ezekiel\Active\Relations\HasOne;
use Rougin\Ezekiel\Query;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
abstract class Model
{
    /**
     * @var string[]
     */
    protected $appends = array();

    /**
     * @var array<string, mixed>
     */
    protected $attributes = array();

    /**
     * @var array<string, string>
     */
    protected $casts = array();

    /**
     * @var string
     */
    protected $connection = 'default';

    /**
     * @var boolean
     */
    protected $exists = false;

    /**
     * @var string[]
     */
    protected $fillable = array();

    /**
     * @var string[]
     */
    protected $guarded = array('*');

    /**
     * @var boolean
     */
    protected $incrementing = true;

    /**
     * @var string
     */
    protected $keyType = 'int';

    /**
     * @var array<string, mixed>
     */
    protected $original = array();

    /**
     * @var \PDO|null
     */
    protected $pdo;

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
    protected $softDelete = false;

    /**
     * @var string|null
     */
    protected $table = null;

    /**
     * @var boolean
     */
    protected $timestamps = true;

    /**
     * @var boolean
     */
    protected $wasRecentlyCreated = false;

    /**
     * @var string[]
     */
    protected $with = array();

    /**
     * @param string       $method
     * @param array<mixed> $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $query = $this->newQuery();

        /** @var callable $callback */
        $callback = array($query, $method);

        return call_user_func_array($callback, $parameters);
    }

    /**
     * @param array<string, mixed> $attrs
     */
    public function __construct(array $attrs = array())
    {
        $this->fill($attrs);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * @param string      $related
     * @param string|null $foreignKey
     * @param string|null $ownerKey
     *
     * @return \Rougin\Ezekiel\Active\Relations\BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null)
    {
        return new BelongsTo($this, $related, $foreignKey, $ownerKey);
    }

    /**
     * @param string      $related
     * @param string|null $table
     * @param string|null $foreignPivotKey
     * @param string|null $relatedPivotKey
     * @param string|null $parentKey
     * @param string|null $relatedKey
     *
     * @return \Rougin\Ezekiel\Active\Relations\BelongsToMany
     */
    public function belongsToMany(
        $related,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null
    ) {
        /** @var \Rougin\Ezekiel\Active\Model $instance */
        $instance = new $related;

        if (is_null($table))
        {
            $table = $this->joiningTable($related);
        }

        if (is_null($foreignPivotKey))
        {
            $foreignPivotKey = $this->getForeignKey();
        }

        if (is_null($relatedPivotKey))
        {
            $relatedPivotKey = $instance->getForeignKey();
        }

        if (is_null($parentKey))
        {
            $parentKey = $this->getKeyName();
        }

        if (is_null($relatedKey))
        {
            $relatedKey = $instance->getKeyName();
        }

        return new BelongsToMany(
            $this,
            $related,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey
        );
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function castAttribute($key, $value)
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
            return (int) $value;
        }

        if ($type === 'boolean' || $type === 'bool')
        {
            return (bool) $value;
        }

        if ($type === 'float' || $type === 'double' || $type === 'real')
        {
            return (float) $value;
        }

        if ($type === 'string')
        {
            return (string) $value;
        }

        return $value;
    }

    /**
     * @return boolean|null
     */
    public function delete()
    {
        if (! $this->exists)
        {
            return null;
        }

        if ($this->softDelete)
        {
            return $this->runSoftDelete();
        }

        return $this->runHardDelete();
    }

    /**
     * @param array<string, mixed> $attrs
     *
     * @return $this
     */
    public function fill(array $attrs)
    {
        foreach ($attrs as $key => $value)
        {
            if ($this->isFillable($key))
            {
                $this->setAttribute($key, $value);
            }
        }

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function forceDelete()
    {
        return $this->runHardDelete();
    }

    /**
     * @param array<string, mixed> $attrs
     *
     * @return $this
     */
    public function forceFill(array $attrs)
    {
        foreach ($attrs as $key => $value)
        {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getAccessorMethod($key)
    {
        $parts = explode('_', $key);

        $camel = '';

        foreach ($parts as $part)
        {
            $camel .= ucfirst($part);
        }

        return 'get' . $camel . 'Attribute';
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        $accessor = $this->getAccessorMethod($key);

        if ($accessor && method_exists($this, $accessor))
        {
            $raw = null;

            if (array_key_exists($key, $this->attributes))
            {
                $raw = $this->attributes[$key];
            }

            /** @var callable $callback */
            $callback = array($this, $accessor);

            return call_user_func($callback, $raw);
        }

        if (array_key_exists($key, $this->relations))
        {
            return $this->relations[$key];
        }

        $method = $this->getRelationMethod($key);

        if ($method && method_exists($this, $method))
        {
            /** @var callable $callback */
            $callback = array($this, $method);

            /** @var \Rougin\Ezekiel\Active\Relations\Relation $relation */
            $relation = call_user_func($callback);

            $this->relations[$key] = $relation->getResults();

            return $this->relations[$key];
        }

        $value = null;

        if (array_key_exists($key, $this->attributes))
        {
            $value = $this->attributes[$key];
        }

        return $this->castAttribute($key, $value);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getAttributesForSave()
    {
        return $this->attributes;
    }

    /**
     * @return string|null
     */
    public function getConnectionName()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getCreatedAtColumn()
    {
        $result = defined('static::CREATED_AT') ? static::CREATED_AT : 'created_at';

        return (string) $result;
    }

    /**
     * @return string
     */
    public function getDeletedAtColumn()
    {
        $result = defined('static::DELETED_AT') ? static::DELETED_AT : 'deleted_at';

        return (string) $result;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDirty()
    {
        $dirty = array();

        foreach ($this->attributes as $key => $value)
        {
            if (! array_key_exists($key, $this->original))
            {
                $dirty[$key] = $value;
            }
            elseif ($value !== $this->original[$key])
            {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
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
     * @return boolean
     */
    public function getIncrementing()
    {
        return $this->incrementing;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        $key = $this->getKeyName();

        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    public function getKeyType()
    {
        return $this->keyType;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getMutatorMethod($key)
    {
        $parts = explode('_', $key);

        $camel = '';

        foreach ($parts as $part)
        {
            $camel .= ucfirst($part);
        }

        return 'set' . $camel . 'Attribute';
    }

    /**
     * @return \PDO|null
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @return string
     */
    public function getQualifiedDeletedAtColumn()
    {
        return $this->qualifyColumn($this->getDeletedAtColumn());
    }

    /**
     * @return string
     */
    public function getQualifiedKeyName()
    {
        return $this->qualifyColumn($this->getKeyName());
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    protected function getRelationMethod($key)
    {
        return method_exists($this, $key) ? $key : null;
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

        $class = get_class($this);

        $parts = explode('\\', $class);

        $base = end($parts);

        $raw = preg_replace('/([A-Z])/', '_$1', lcfirst($base));

        $snake = strtolower((string) $raw);

        $snake = ltrim($snake, '_');

        return $snake . 's';
    }

    /**
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        $result = defined('static::UPDATED_AT') ? static::UPDATED_AT : 'updated_at';

        return (string) $result;
    }

    /**
     * @param string      $related
     * @param string|null $foreignKey
     * @param string|null $localKey
     *
     * @return \Rougin\Ezekiel\Active\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        return new HasMany($this, $related, $foreignKey, $localKey);
    }

    /**
     * @param string      $related
     * @param string|null $foreignKey
     * @param string|null $localKey
     *
     * @return \Rougin\Ezekiel\Active\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        return new HasOne($this, $related, $foreignKey, $localKey);
    }

    /**
     * @param string $key
     *
     * @return boolean
     */
    public function isFillable($key)
    {
        if (in_array($key, $this->fillable, true))
        {
            return true;
        }

        if ($this->guarded === array('*'))
        {
            return false;
        }

        return ! in_array($key, $this->guarded, true);
    }

    /**
     * @param string $related
     *
     * @return string
     */
    public function joiningTable($related)
    {
        /** @var \Rougin\Ezekiel\Active\Model $instance */
        $instance = new $related;

        $segments = array($this->getTable(), $instance->getTable());

        sort($segments);

        return strtolower($segments[0] . '_' . $segments[1]);
    }

    /**
     * @return \Rougin\Ezekiel\Active\Builder
     */
    public function newQuery()
    {
        $builder = new Builder(get_class($this), $this->pdo, $this->getTable());

        if ($this->softDelete)
        {
            $builder->whereNull($this->getDeletedAtColumn());
        }

        return $builder;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Builder
     */
    protected function newQueryWithoutScopes()
    {
        return new Builder(get_class($this), $this->pdo, $this->getTable());
    }

    /**
     * @return boolean
     */
    protected function performInsert()
    {
        if (! $this->pdo)
        {
            return false;
        }

        /** @var \PDO $execPdo */
        $execPdo = $this->pdo;

        $query = new Query;

        $insert = $query->insertInto($this->getTable());

        $data = $this->getAttributesForSave();

        if ($this->usesTimestamps())
        {
            $now = date('Y-m-d H:i:s');

            $data[$this->getCreatedAtColumn()] = $now;

            $data[$this->getUpdatedAtColumn()] = $now;

            $this->setAttribute($this->getCreatedAtColumn(), $now);

            $this->setAttribute($this->getUpdatedAtColumn(), $now);
        }

        $insert->values($data);

        $sql = $query->toSql();

        $binds = array_values($query->getBinds());

        $stmt = $execPdo->prepare($sql);

        $stmt->execute($binds);

        if ($this->getIncrementing())
        {
            $id = $execPdo->lastInsertId();

            if ($this->getKeyType() === 'int' || $this->getKeyType() === 'integer')
            {
                $id = (int) $id;
            }

            $this->setAttribute($this->getKeyName(), $id);
        }

        $this->exists = true;

        $this->wasRecentlyCreated = true;

        $this->syncOriginal();

        return true;
    }

    /**
     * @return boolean
     */
    protected function performUpdate()
    {
        if (! $this->pdo)
        {
            return false;
        }

        /** @var \PDO $execPdo */
        $execPdo = $this->pdo;

        $dirty = $this->getDirty();

        if (empty($dirty))
        {
            return true;
        }

        if ($this->usesTimestamps())
        {
            $now = date('Y-m-d H:i:s');

            $dirty[$this->getUpdatedAtColumn()] = $now;

            $this->setAttribute($this->getUpdatedAtColumn(), $now);
        }

        $query = new Query;

        $query->update($this->getTable());

        foreach ($dirty as $key => $value)
        {
            $query->set($key, $value);
        }

        $query->where($this->getKeyName())->equals($this->getKey());

        $sql = $query->toSql();

        $binds = array_values($query->getBinds());

        $stmt = $execPdo->prepare($sql);

        $stmt->execute($binds);

        $this->syncOriginal();

        return true;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function qualifyColumn($key)
    {
        if (strpos($key, '.') !== false)
        {
            return $key;
        }

        return $this->getTable() . '.' . $key;
    }

    /**
     * @param mixed                $id
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function update($id, array $data)
    {
        $models = $this->where($this->getKeyName(), '=', $id)->get();

        if (empty($models))
        {
            return $this;
        }

        $model = $models[0];

        $model->fill($data);

        $model->save();

        return $model;
    }

    /**
     * @return boolean
     */
    public function restore()
    {
        if (! $this->softDelete)
        {
            return false;
        }

        $this->setAttribute($this->getDeletedAtColumn(), null);

        return $this->save();
    }

    /**
     * @return boolean
     */
    protected function runHardDelete()
    {
        if (! $this->pdo)
        {
            return false;
        }

        /** @var \PDO $execPdo */
        $execPdo = $this->pdo;

        $query = new Query;

        $query->deleteFrom($this->getTable());

        $query->where($this->getKeyName())->equals($this->getKey());

        $sql = $query->toSql();

        $binds = array_values($query->getBinds());

        $stmt = $execPdo->prepare($sql);

        $stmt->execute($binds);

        $this->exists = false;

        return true;
    }

    /**
     * @return boolean
     */
    protected function runSoftDelete()
    {
        $now = date('Y-m-d H:i:s');

        $this->setAttribute($this->getDeletedAtColumn(), $now);

        return $this->save();
    }

    /**
     * @return boolean
     */
    public function save()
    {
        if ($this->exists)
        {
            return $this->performUpdate();
        }

        return $this->performInsert();
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $mutator = $this->getMutatorMethod($key);

        if ($mutator && method_exists($this, $mutator))
        {
            /** @var callable $callback */
            $callback = array($this, $mutator);

            call_user_func($callback, $value);

            return;
        }

        $this->attributes[$key] = $value;
    }

    /**
     * @param \PDO $pdo
     *
     * @return $this
     */
    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;

        return $this;
    }

    /**
     * @param array<string, mixed> $attributes
     * @param boolean              $sync
     *
     * @return $this
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;

        if ($sync)
        {
            $this->syncOriginal();
        }

        $this->exists = true;

        return $this;
    }

    /**
     * @param string $relation
     * @param mixed  $value
     *
     * @return void
     */
    public function setRelation($relation, $value)
    {
        $this->relations[$relation] = $value;
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return void
     */
    protected function syncOriginal()
    {
        $this->original = $this->attributes;
    }

    /**
     * @return boolean
     */
    public function trashed()
    {
        $column = $this->getDeletedAtColumn();

        if (! isset($this->attributes[$column]))
        {
            return false;
        }

        /** @var mixed $value */
        $value = $this->attributes[$column];

        return $value !== null;
    }

    /**
     * @return boolean
     */
    public function usesTimestamps()
    {
        return $this->timestamps;
    }
}

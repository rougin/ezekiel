<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Dialect;
use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\Result;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Depot
{
    /**
     * @var \Rougin\Ezekiel\Active\Manager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Rougin\Ezekiel\Result|null
     */
    protected $result = null;

    /**
     * @param \Rougin\Ezekiel\Active\Manager $manager
     * @param string                         $name
     */
    public function __construct(Manager $manager, $name)
    {
        $this->manager = $manager;

        $this->name = $name;
    }

    /**
     * @param \Rougin\Ezekiel\Active\Builder $builder
     *
     * @return integer
     */
    public function count(Builder $builder)
    {
        $query = $builder->toCountQuery();

        $sql = $query->toSql();

        $binds = $query->getBinds();

        $flat = array();

        foreach ($binds as $value)
        {
            if (! is_array($value))
            {
                $flat[] = $value;
            }

            if (is_array($value))
            {
                foreach ($value as $item)
                {
                    $flat[] = $item;
                }
            }
        }

        $stmt = $this->getPdo()->prepare($sql);

        $stmt->execute($flat);

        /** @var integer */
        return $stmt->fetchColumn();
    }

    /**
     * @param string  $table
     * @param string  $pk
     * @param mixed   $id
     * @param boolean $softDeletes
     *
     * @return boolean
     */
    public function deleteRow($table, $pk, $id, $softDeletes)
    {
        $query = $this->newQuery();

        $query->deleteFrom($table);

        if ($softDeletes)
        {
            $query = $this->newQuery();

            $now = date('Y-m-d H:i:s');

            $query->update($table);

            $query->set('deleted_at', $now);
        }

        $query->where($pk)->equals($id);

        $sql = $query->toSql();

        $binds = array_values($query->getBinds());

        $stmt = $this->getPdo()->prepare($sql);

        return $stmt->execute($binds);
    }

    /**
     * @param \Rougin\Ezekiel\Active\Builder $builder
     *
     * @return array<integer, array<string, string>>
     */
    public function get(Builder $builder)
    {
        $query = $builder->toQuery();

        $result = $this->getResult();

        /** @var array<integer, array<string, string>> */
        $rows = $result->items($query);

        return $rows;
    }

    /**
     * @param string               $table
     * @param array<string, mixed> $data
     *
     * @return string
     */
    public function insert($table, $data)
    {
        $query = $this->newQuery();

        $query->insert_into($table)->values($data);

        $sql = $query->toSql();

        $binds = array_values($query->getBinds());

        $pdo = $this->getPdo();

        $stmt = $pdo->prepare($sql);

        $stmt->execute($binds);

        /** @var string */
        return $pdo->lastInsertId();
    }

    /**
     * @param string               $table
     * @param string               $pk
     * @param mixed                $id
     * @param array<string, mixed> $data
     *
     * @return boolean
     */
    public function updateRow($table, $pk, $id, $data)
    {
        $query = $this->newQuery();

        $query->update($table);

        foreach ($data as $key => $value)
        {
            $query->set($key, $value);
        }

        $query->where($pk)->equals($id);

        $sql = $query->toSql();

        $binds = array_values($query->getBinds());

        $stmt = $this->getPdo()->prepare($sql);

        return $stmt->execute($binds);
    }

    /**
     * @return \PDO
     */
    protected function getPdo()
    {
        return $this->manager->get($this->name);
    }

    /**
     * @return \Rougin\Ezekiel\Result
     */
    protected function getResult()
    {
        if ($this->result === null)
        {
            $this->result = new Result($this->getPdo());
        }

        return $this->result;
    }

    /**
     * @return \Rougin\Ezekiel\Query
     */
    protected function newQuery()
    {
        $query = new Query;

        $pdo = $this->getPdo();

        $dialect = Dialect::fromPdo($pdo);

        $query->setDialect($dialect);

        return $query;
    }
}

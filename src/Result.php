<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Result
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     *
     * @return mixed
     */
    public function first(Query $query)
    {
        $stmt = $this->execute($query);

        /** @var array<string, mixed> */
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($query->isEntity())
        {
            return $this->resolve($query, $result);
        }

        return $result;
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     *
     * @return mixed[]
     */
    public function items(Query $query)
    {
        $stmt = $this->execute($query);

        /** @var array<string, mixed>[] */
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (! $query->isEntity())
        {
            return $items;
        }

        foreach ($items as $key => $row)
        {
            $items[$key] = $this->resolve($query, $row);
        }

        return $items;
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     *
     * @return \PDOStatement
     */
    protected function execute(Query $query)
    {
        $sql = $query->toSql();

        $stmt = $this->pdo->prepare($sql);

        $binds = $query->getBinds();

        $stmt->execute(array_values($binds));

        return $stmt;
    }

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param array<string, mixed>  $data
     *
     * @return mixed
     */
    protected function resolve($query, $data)
    {
        $class = new \ReflectionClass($query);

        foreach ($data as $key => $value)
        {
            if (! $class->hasProperty($key))
            {
                continue;
            }

            $prop = $class->getProperty($key);

            if (! $prop->isPublic())
            {
                $prop->setAccessible(true);
            }

            $prop->setValue($query, $value);

            if (! $prop->isPublic())
            {
                $prop->setAccessible(false);
            }
        }

        return $query;
    }
}

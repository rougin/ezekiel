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
     * @var \Rougin\Ezekiel\DialectInterface
     */
    protected $dialect;

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->dialect = Dialect::fromPdo($pdo);

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

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (! $query->isEntity())
        {
            return $result;
        }

        if ($result === false)
        {
            return $query;
        }

        /** @var array<string, mixed> $result */
        return $this->resolve($query, $result);
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
        $query->setDialect($this->dialect);

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

            if (! $prop->isPublic() && PHP_VERSION_ID < 80500)
            {
                $prop->setAccessible(true);
            }

            $prop->setValue($query, $value);

            if (! $prop->isPublic() && PHP_VERSION_ID < 80500)
            {
                $prop->setAccessible(false);
            }
        }

        return $query;
    }
}

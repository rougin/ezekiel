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
     * Returns the first item from the query.
     *
     * @param \Rougin\Ezekiel\Query $query
     *
     * @return mixed
     */
    public function first(Query $query)
    {
        $sql = $query->toSql();

        $stmt = $this->pdo->prepare($sql);

        $binds = $query->getBinds();

        $stmt->execute(array_values($binds));

        $type = \PDO::FETCH_ASSOC;

        return $stmt->fetch($type);
    }

    /**
     * Returns the items from the query.
     *
     * @param \Rougin\Ezekiel\Query $query
     *
     * @return mixed
     */
    public function get(Query $query)
    {
        $sql = $query->toSql();

        $stmt = $this->pdo->prepare($sql);

        $binds = $query->getBinds();

        $stmt->execute(array_values($binds));

        $type = \PDO::FETCH_ASSOC;

        return $stmt->fetchAll($type);
    }
}

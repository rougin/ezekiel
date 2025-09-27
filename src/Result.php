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
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @param \PDO                  $pdo
     * @param \Rougin\Ezekiel\Query $query
     */
    public function __construct(\PDO $pdo, Query $query)
    {
        $this->query = $query;

        $this->pdo = $pdo;
    }

    /**
     * Returns the items from the query.
     *
     * @return mixed
     */
    public function toItems()
    {
        $sql = $this->query->toSql();

        $stmt = $this->pdo->prepare($sql);

        $binds = $this->query->getBinds();

        $stmt->execute(array_values($binds));

        $type = \PDO::FETCH_ASSOC;

        return $stmt->fetchAll($type);
    }
}

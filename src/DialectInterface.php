<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
interface DialectInterface
{
    /**
     * Returns the dialect name (e.g., "mysql", "pgsql", "sqlite", "mssql").
     *
     * @return string
     */
    public function name();

    /**
     * Generates the platform-specific LIMIT and OFFSET clause.
     *
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function limitClause($limit, $offset);

    /**
     * Quotes an identifier (table name, column name, alias, etc.).
     *
     * @param string $name
     *
     * @return string
     */
    public function quoteIdentifier($name);

    /**
     * Returns true if the dialect supports RIGHT JOIN.
     *
     * @return boolean
     */
    public function supportsRightJoin();
}

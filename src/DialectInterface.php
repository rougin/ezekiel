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
     * Returns true if the dialect supports RIGHT JOIN.
     *
     * @return boolean
     */
    public function canRightJoin();

    /**
     * Returns the dialect name (e.g., "mysql", "pgsql", "sqlite", "mssql").
     *
     * @return string
     */
    public function getName();

    /**
     * Quotes an identifier (table name, column name, alias, etc.).
     *
     * @param string $name
     *
     * @return string
     */
    public function quote($name);

    /**
     * Generates the platform-specific LIMIT and OFFSET clause.
     *
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function toLimit($limit, $offset);
}

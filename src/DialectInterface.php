<?php

namespace Rougin\Ezekiel;

use Rougin\Ezekiel\Schema\Column;

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
     * Generates the ALTER TABLE statement.
     *
     * @param string $table
     * @param string $columns
     *
     * @return string
     */
    public function toAlterTable($table, $columns);

    /**
     * Generates the column definition SQL.
     *
     * @param \Rougin\Ezekiel\Schema\Column $column
     *
     * @return string
     */
    public function toColumn(Column $column);

    /**
     * Generates the CREATE TABLE statement.
     *
     * @param string $table
     * @param string $columns
     *
     * @return string
     */
    public function toCreateTable($table, $columns);

    /**
     * Generates the DROP TABLE statement.
     *
     * @param string $table
     *
     * @return string
     */
    public function toDropTable($table);

    /**
     * Generates the DROP TABLE IF EXISTS statement.
     *
     * @param string $table
     *
     * @return string
     */
    public function toDropTableIfExists($table);

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

<?php

namespace Rougin\Ezekiel\Dialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SqliteDialect extends AbstractDialect
{
    /**
     * @return boolean
     */
    public function canRightJoin()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sqlite';
    }

    /**
     * @return string
     */
    public function getOpenQuoteChar()
    {
        return '"';
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function toLimit($limit, $offset)
    {
        return ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }
}

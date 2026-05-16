<?php

namespace Rougin\Ezekiel\Dialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class PgsqlDialect extends AbstractDialect
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'pgsql';
    }

    /**
     * @return string
     */
    public function getQuoteChar()
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

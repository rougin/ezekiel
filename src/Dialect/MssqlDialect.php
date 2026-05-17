<?php

namespace Rougin\Ezekiel\Dialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class MssqlDialect extends AbstractDialect
{
    /**
     * @return string
     */
    public function getCloseQuoteChar()
    {
        return ']';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mssql';
    }

    /**
     * @return string
     */
    public function getOpenQuoteChar()
    {
        return '[';
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function toLimit($limit, $offset)
    {
        return ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $limit . ' ROWS ONLY';
    }
}

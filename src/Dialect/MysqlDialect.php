<?php

namespace Rougin\Ezekiel\Dialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class MysqlDialect extends AbstractDialect
{
    /**
     * @return string
     */
    public function getQuoteChar()
    {
        return '`';
    }

    /**
     * @return string
     */
    public function name()
    {
        return 'mysql';
    }
}

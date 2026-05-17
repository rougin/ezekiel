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
    public function getName()
    {
        return 'mysql';
    }

    /**
     * @return string
     */
    public function getOpenQuoteChar()
    {
        return '`';
    }
}

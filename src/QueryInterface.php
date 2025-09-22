<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
interface QueryInterface
{
    /**
     * @return integer
     */
    public function getType();

    /**
     * @return string
     */
    public function toSql();
}

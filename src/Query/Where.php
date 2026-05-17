<?php

namespace Rougin\Ezekiel\Query;

use Rougin\Ezekiel\Query;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Where extends Compare
{
    /**
     * @var integer
     */
    protected $type = Query::TYPE_WHERE;
}

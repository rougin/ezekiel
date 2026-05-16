<?php

namespace Rougin\Ezekiel\Query;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Having extends Compare
{
    /**
     * @var integer
     */
    protected $type = \Rougin\Ezekiel\Query::TYPE_HAVING;
}

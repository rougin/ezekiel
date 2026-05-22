<?php

namespace Rougin\Ezekiel\Query;

use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\QueryInterface;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class WhereGroup implements QueryInterface
{
    /**
     * @var mixed[]
     */
    protected $binds = array();

    /**
     * @var integer
     */
    protected $group;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @param \Rougin\Ezekiel\Query $inner
     * @param integer               $group
     */
    public function __construct(Query $inner, $group)
    {
        $this->group = $group;

        $items = array();

        foreach ($inner->getItems() as $item)
        {
            if ($item->getType() !== Query::TYPE_WHERE)
            {
                continue;
            }

            if ($item instanceof Compare)
            {
                $value = $item->getValues();

                $this->binds = $inner->mergeBinds($this->binds, $value);
            }

            $sql = $item->toSql();

            $sql = preg_replace('/^(WHERE|AND|OR) /', '', $sql, 1);

            $items[] = $sql;
        }

        $prefix = $this->setPrefix();

        $this->sql = $prefix . '(' . implode(' ', $items) . ')';
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return Query::TYPE_WHERE;
    }

    /**
     * @return mixed[]
     */
    public function getValues()
    {
        return $this->binds;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return $this->sql;
    }

    /**
     * @return string
     */
    protected function setPrefix()
    {
        return Compare::groupToStr($this->group, 'WHERE ');
    }

}

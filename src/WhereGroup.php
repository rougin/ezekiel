<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class WhereGroup implements QueryInterface
{
    /**
     * @var array<string, mixed>
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
                $this->binds = array_merge($this->binds, $item->getValues());
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
     * @return array<string, mixed>
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
        $prefix = 'WHERE ';

        if ($this->group === Compare::GROUP_AND)
        {
            $prefix = 'AND ';
        }

        if ($this->group === Compare::GROUP_OR)
        {
            $prefix = 'OR ';
        }

        return $prefix;
    }
}

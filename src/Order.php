<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Order implements QueryInterface
{
    const SORT_ASC = 0;

    const SORT_DESC = 1;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @var integer
     */
    protected $sort;

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param string                $key
     */
    public function __construct(Query $query, $key)
    {
        $this->key = $key;

        $this->query = $query;
    }

    /**
     * @return \Rougin\Ezekiel\Query
     */
    public function asc()
    {
        $this->sort = self::SORT_ASC;

        return $this->query->addItem($this);
    }

    /**
     * @return \Rougin\Ezekiel\Query
     */
    public function desc()
    {
        $this->sort = self::SORT_DESC;

        return $this->query->addItem($this);
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return Query::TYPE_ORDER;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $sort = $this->sort === self::SORT_ASC ? 'ASC' : 'DESC';

        return 'ORDER BY ' . $this->key . ' ' . $sort;
    }
}

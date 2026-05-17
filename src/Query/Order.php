<?php

namespace Rougin\Ezekiel\Query;

use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\QueryInterface;

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
        $dialect = $this->query->getDialect();

        $sort = 'DESC';

        if ($this->sort === self::SORT_ASC)
        {
            $sort = 'ASC';
        }

        $key = $dialect->quote($this->key);

        return 'ORDER BY ' . $key . ' ' . $sort;
    }
}

<?php

namespace Rougin\Ezekiel\Query;

use Rougin\Ezekiel\QueryInterface;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Join implements QueryInterface
{
    const TYPE_INNER = 0;

    const TYPE_LEFT = 1;

    const TYPE_RIGHT = 2;

    /**
     * @var string
     */
    protected $foreign;

    /**
     * @var string
     */
    protected $local;

    /**
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @param \Rougin\Ezekiel\Query $query
     * @param string                $table
     * @param integer               $type
     */
    public function __construct(\Rougin\Ezekiel\Query $query, $table, $type = self::TYPE_INNER)
    {
        $this->query = $query;

        $this->type = $type;

        $this->table = $table;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return \Rougin\Ezekiel\Query::TYPE_JOIN;
    }

    /**
     * @param string $local
     * @param string $foreign
     *
     * @return \Rougin\Ezekiel\Query
     */
    public function on($local, $foreign)
    {
        $this->local = $local;

        $this->foreign = $foreign;

        return $this->query->addItem($this);
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $dialect = $this->query->getDialect();

        $join = 'INNER JOIN';

        if ($this->type === self::TYPE_LEFT)
        {
            $join = 'LEFT JOIN';
        }

        if ($this->type === self::TYPE_RIGHT)
        {
            $join = 'LEFT JOIN';

            if ($dialect->supportsRightJoin())
            {
                $join = 'RIGHT JOIN';
            }
        }

        $table = $dialect->quoteIdentifier($this->table);

        $local = $dialect->quoteIdentifier($this->local);

        $foreign = $dialect->quoteIdentifier($this->foreign);

        $sql = sprintf('%s %s ON %s = %s', $join, $table, $local, $foreign);

        return $sql;
    }
}

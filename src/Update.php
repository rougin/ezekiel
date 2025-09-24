<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Update
{
    /**
     * @var \Rougin\Ezekiel\Query
     */
    protected $query;

    /**
     * @var array<string, mixed>
     */
    protected $values = array();

    /**
     * @param \Rougin\Ezekiel\Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $items = array();

        foreach ($this->values as $name => $value)
        {
            $items[] = $name . ' = ?';
        }

        $table = $this->query->getTable();

        $keys = implode(', ', $items);

        return 'UPDATE ' . $table . ' SET ' . $keys;
    }
}

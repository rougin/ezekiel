<?php

namespace Rougin\Ezekiel\Schema;

use Rougin\Ezekiel\DialectInterface;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Column
{
    /**
     * @var boolean
     */
    protected $autoIncrement = false;

    /**
     * @var mixed|null
     */
    protected $default = null;

    /**
     * @var boolean
     */
    protected $hasDefault = false;

    /**
     * @var integer|string|null
     */
    protected $length;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $nullable = false;

    /**
     * @var boolean
     */
    protected $primary = false;

    /**
     * @var boolean
     */
    protected $unique = false;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string              $name
     * @param string              $type
     * @param integer|string|null $length
     */
    public function __construct($name, $type, $length = null)
    {
        $this->name = $name;

        $this->type = $type;

        $this->length = $length;
    }

    /**
     * @return self
     */
    public function autoIncrement()
    {
        $this->autoIncrement = true;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function defaultValue($value)
    {
        $this->default = $value;

        $this->hasDefault = true;

        return $this;
    }

    /**
     * @param boolean $value
     *
     * @return self
     */
    public function nullable($value = true)
    {
        $this->nullable = $value;

        return $this;
    }

    /**
     * @return self
     */
    public function primary()
    {
        $this->primary = true;

        return $this;
    }

    /**
     * @return self
     */
    public function unique()
    {
        $this->unique = true;

        return $this;
    }

    /**
     * @param \Rougin\Ezekiel\DialectInterface $dialect
     *
     * @return string
     */
    public function compile(DialectInterface $dialect)
    {
        $sql = $dialect->quote($this->name);

        $sql .= ' ' . $this->type;

        if ($this->length !== null)
        {
            $sql .= '(' . $this->length . ')';
        }

        if (! $this->nullable)
        {
            $sql .= ' NOT NULL';
        }

        if ($this->hasDefault)
        {
            if (is_bool($this->default))
            {
                $sql .= ' DEFAULT ' . (int) $this->default;
            }

            if (is_int($this->default) || is_float($this->default))
            {
                $sql .= ' DEFAULT ' . $this->default;
            }

            if (is_string($this->default))
            {
                $sql .= ' DEFAULT \'' . $this->default . '\'';
            }
        }

        if ($this->autoIncrement)
        {
            $sql .= ' AUTO_INCREMENT';
        }

        if ($this->unique)
        {
            $sql .= ' UNIQUE';
        }

        if ($this->primary)
        {
            $sql .= ' PRIMARY KEY';
        }

        return $sql;
    }
}

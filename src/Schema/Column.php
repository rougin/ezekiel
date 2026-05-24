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
    protected $length = null;

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
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return integer|string|null
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function hasDefault()
    {
        return $this->hasDefault;
    }

    /**
     * @return boolean
     */
    public function isAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @return boolean
     */
    public function isPrimary()
    {
        return $this->primary;
    }

    /**
     * @return boolean
     */
    public function isUnique()
    {
        return $this->unique;
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
     * @param \Rougin\Ezekiel\DialectInterface $dialect
     *
     * @return string
     */
    public function toSql(DialectInterface $dialect)
    {
        return $dialect->toColumn($this);
    }

    /**
     * @return self
     */
    public function unique()
    {
        $this->unique = true;

        return $this;
    }
}

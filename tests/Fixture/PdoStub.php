<?php

namespace Rougin\Ezekiel\Fixture;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class PdoStub extends \PDO
{
    /**
     * @var string
     */
    protected $driver;

    /**
     * @param string $driver
     */
    public function __construct($driver)
    {
        parent::__construct('sqlite::memory:');

        $this->driver = $driver;
    }

    /**
     * @param integer $attribute
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function getAttribute($attribute)
    {
        $value = parent::getAttribute($attribute);

        $isDriver = $attribute === \PDO::ATTR_DRIVER_NAME;

        return $isDriver ? $this->driver : $value;
    }
}

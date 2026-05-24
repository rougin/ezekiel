<?php

namespace Rougin\Ezekiel\Active;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Manager
{
    /**
     * @var array<string, \PDO>
     */
    protected static $pdos = array();

    /**
     * @param string $name
     * @param \PDO   $pdo
     *
     * @return void
     */
    public static function set($name, \PDO $pdo)
    {
        static::$pdos[$name] = $pdo;
    }

    /**
     * @param string $name
     *
     * @return \PDO
     */
    public function get($name)
    {
        return static::$pdos[$name];
    }
}

<?php

namespace Rougin\Ezekiel\Schema;

use Rougin\Ezekiel\DialectInterface;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Design
{
    /**
     * @var \Rougin\Ezekiel\Schema\Column[]
     */
    protected $columns = array();

    /**
     * @var array<int, array{type: string, columns: string[]}>
     */
    protected $indexes = array();

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function bigInteger($name)
    {
        return $this->addColumn($name, 'BIGINT');
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function boolean($name)
    {
        return $this->addColumn($name, 'TINYINT', 1);
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function date($name)
    {
        return $this->addColumn($name, 'DATE');
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function dateTime($name)
    {
        return $this->addColumn($name, 'DATETIME');
    }

    /**
     * @param string  $name
     * @param integer $precision
     * @param integer $scale
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function decimal($name, $precision = 8, $scale = 2)
    {
        return $this->addColumn($name, 'DECIMAL', $precision . ', ' . $scale);
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function float($name)
    {
        return $this->addColumn($name, 'FLOAT');
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function increments($name)
    {
        return $this->integer($name)->autoIncrement()->primary();
    }

    /**
     * @param string|string[] $columns
     *
     * @return self
     */
    public function index($columns)
    {
        return $this->addIndex('INDEX', $columns);
    }

    /**
     * @param string       $name
     * @param integer|null $length
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function integer($name, $length = null)
    {
        return $this->addColumn($name, 'INT', $length);
    }

    /**
     * @param string|string[] $columns
     *
     * @return self
     */
    public function primary($columns)
    {
        return $this->addIndex('PRIMARY KEY', $columns);
    }

    /**
     * @return void
     */
    public function softDeletes()
    {
        $this->timestamp('deleted_at')->nullable();
    }

    /**
     * @param string       $name
     * @param integer|null $length
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function string($name, $length = 255)
    {
        return $this->addColumn($name, 'VARCHAR', $length);
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function text($name)
    {
        return $this->addColumn($name, 'TEXT');
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function timestamp($name)
    {
        return $this->addColumn($name, 'TIMESTAMP');
    }

    /**
     * @return void
     */
    public function timestamps()
    {
        $this->timestamp('created_at')->nullable();

        $this->timestamp('updated_at')->nullable();
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    public function tinyInteger($name)
    {
        return $this->addColumn($name, 'TINYINT');
    }

    /**
     * @param string|string[] $columns
     *
     * @return self
     */
    public function unique($columns)
    {
        return $this->addIndex('UNIQUE', $columns);
    }

    /**
     * @param \Rougin\Ezekiel\DialectInterface $dialect
     *
     * @return string
     */
    public function compile(DialectInterface $dialect)
    {
        $lines = array();

        foreach ($this->columns as $column)
        {
            $lines[] = $column->toSql($dialect);
        }

        foreach ($this->indexes as $index)
        {
            $type = $index['type'];

            /** @var string[] */
            $columns = $index['columns'];

            $quoted = array();

            foreach ($columns as $column)
            {
                $quoted[] = $dialect->quote($column);
            }

            $lines[] = $type . ' (' . implode(', ', $quoted) . ')';
        }

        return implode(', ', $lines);
    }

    /**
     * @param string              $name
     * @param string              $type
     * @param integer|string|null $length
     *
     * @return \Rougin\Ezekiel\Schema\Column
     */
    protected function addColumn($name, $type, $length = null)
    {
        $column = new Column($name, $type, $length);

        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param string          $type
     * @param string|string[] $columns
     *
     * @return self
     */
    protected function addIndex($type, $columns)
    {
        if (is_string($columns))
        {
            $columns = array($columns);
        }

        /** @var string[] $columns */

        $this->indexes[] = array('type' => $type, 'columns' => $columns);

        return $this;
    }
}

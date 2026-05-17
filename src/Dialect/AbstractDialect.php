<?php

namespace Rougin\Ezekiel\Dialect;

use Rougin\Ezekiel\DialectInterface;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
abstract class AbstractDialect implements DialectInterface
{
    /**
     * @return boolean
     */
    public function canRightJoin()
    {
        return true;
    }

    /**
     * Returns the opening quote character.
     *
     * @return string
     */
    abstract public function getOpenQuoteChar();

    /**
     * Returns the closing quote character. Defaults to the
     * open character for dialects with symmetric quoting.
     *
     * @return string
     */
    public function getCloseQuoteChar()
    {
        return $this->getOpenQuoteChar();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function quote($name)
    {
        $close = $this->getCloseQuoteChar();

        $open = $this->getOpenQuoteChar();

        if (strlen($name) > 0 && $name[0] === $open)
        {
            return $name;
        }

        if ($name === '*')
        {
            return $name;
        }

        if (strpos($name, '(') !== false)
        {
            return $name;
        }

        if (is_numeric($name[0]))
        {
            return $name;
        }

        if (strpos($name, '.') !== false)
        {
            $parts = explode('.', $name);

            foreach ($parts as &$part)
            {
                if ($part !== '*')
                {
                    $part = $open . $part . $close;
                }
            }

            return implode('.', $parts);
        }

        if (strpos($name, ' ') !== false)
        {
            $parts = explode(' ', $name);

            $table = $open . $parts[0] . $close;

            $rest = array();

            $count = count($parts);

            for ($i = 1; $i < $count; $i++)
            {
                $p = $parts[$i];

                if ($p === 'AS' || $p === 'as' || $p === '')
                {
                    $rest[] = $p;

                    continue;
                }

                $rest[] = $open . $p . $close;
            }

            return $table . ' ' . implode(' ', $rest);
        }

        return $open . $name . $close;
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function toLimit($limit, $offset)
    {
        return ' LIMIT ' . $limit . ', ' . $offset;
    }
}

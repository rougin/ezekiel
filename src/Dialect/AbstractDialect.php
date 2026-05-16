<?php

namespace Rougin\Ezekiel\Dialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
abstract class AbstractDialect implements \Rougin\Ezekiel\DialectInterface
{
    /**
     * Returns the quoting character (e.g., "`", "\"", "[").
     *
     * @return string
     */
    abstract public function getQuoteChar();

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function limitClause($limit, $offset)
    {
        return ' LIMIT ' . $limit . ', ' . $offset;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function quoteIdentifier($name)
    {
        $char = $this->getQuoteChar();

        if (strlen($name) > 0 && $name[0] === $char)
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
                    $part = $char . $part . $char;
                }
            }

            return implode('.', $parts);
        }

        if (strpos($name, ' ') !== false)
        {
            $parts = explode(' ', $name);

            $table = $char . $parts[0] . $char;

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

                $rest[] = $char . $p . $char;
            }

            return $table . ' ' . implode(' ', $rest);
        }

        return $char . $name . $char;
    }

    /**
     * @return boolean
     */
    public function supportsRightJoin()
    {
        return true;
    }
}

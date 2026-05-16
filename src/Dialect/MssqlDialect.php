<?php

namespace Rougin\Ezekiel\Dialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class MssqlDialect extends AbstractDialect
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'mssql';
    }

    /**
     * @return string
     */
    public function getQuoteChar()
    {
        return '[';
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function quote($name)
    {
        if (strlen($name) > 0 && $name[0] === '[')
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
                    $part = '[' . $part . ']';
                }
            }

            return implode('.', $parts);
        }

        if (strpos($name, ' ') !== false)
        {
            $parts = explode(' ', $name);

            $table = '[' . $parts[0] . ']';

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

                $rest[] = '[' . $p . ']';
            }

            return $table . ' ' . implode(' ', $rest);
        }

        return '[' . $name . ']';
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return string
     */
    public function toLimit($limit, $offset)
    {
        return ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $limit . ' ROWS ONLY';
    }
}

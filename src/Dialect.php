<?php

namespace Rougin\Ezekiel;

use Rougin\Ezekiel\Dialect\MssqlDialect;
use Rougin\Ezekiel\Dialect\MysqlDialect;
use Rougin\Ezekiel\Dialect\PgsqlDialect;
use Rougin\Ezekiel\Dialect\SqliteDialect;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Dialect
{
    /**
     * Creates a dialect from a PDO instance by detecting its driver.
     *
     * @param \PDO $pdo
     *
     * @return \Rougin\Ezekiel\DialectInterface
     */
    public static function fromPdo(\PDO $pdo)
    {
        $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if ($driver === 'pgsql')
        {
            return new PgsqlDialect;
        }

        if ($driver === 'sqlite')
        {
            return new SqliteDialect;
        }

        if ($driver === 'dblib' || $driver === 'sqlsrv')
        {
            return new MssqlDialect;
        }

        return new MysqlDialect;
    }
}

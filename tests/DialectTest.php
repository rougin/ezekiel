<?php

namespace Rougin\Ezekiel;

use Rougin\Ezekiel\Dialect\MssqlDialect;
use Rougin\Ezekiel\Dialect\MysqlDialect;
use Rougin\Ezekiel\Dialect\PgsqlDialect;
use Rougin\Ezekiel\Dialect\SqliteDialect;
use Rougin\Ezekiel\Fixture\PdoStub;
use Rougin\Ezekiel\Schema\Column;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class DialectTest extends Testcase
{
    /**
     * @return void
     */
    public function test_passed_if_dialect_detects_dblib()
    {
        $expect = 'Rougin\Ezekiel\Dialect\MssqlDialect';

        $pdo = new PdoStub('dblib');

        $actual = Dialect::fromPdo($pdo);

        $this->assertInstanceOf($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_dialect_detects_sqlsrv()
    {
        $expect = 'Rougin\Ezekiel\Dialect\MssqlDialect';

        $pdo = new PdoStub('sqlsrv');

        $actual = Dialect::fromPdo($pdo);

        $this->assertInstanceOf($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_dialect_factory_defaults_to_mysql()
    {
        $expect = 'Rougin\Ezekiel\Dialect\MysqlDialect';

        $pdo = new PdoStub('unknown_driver');

        $actual = Dialect::fromPdo($pdo);

        $this->assertInstanceOf($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_dialect_factory_detects_mysql()
    {
        $expect = 'Rougin\Ezekiel\Dialect\MysqlDialect';

        $pdo = new PdoStub('mysql');

        $actual = Dialect::fromPdo($pdo);

        $this->assertInstanceOf($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_dialect_factory_detects_pgsql()
    {
        $expect = 'Rougin\Ezekiel\Dialect\PgsqlDialect';

        $pdo = new PdoStub('pgsql');

        $actual = Dialect::fromPdo($pdo);

        $this->assertInstanceOf($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_dialect_factory_detects_sqlite()
    {
        $expect = 'Rougin\Ezekiel\Dialect\SqliteDialect';

        $pdo = new \PDO('sqlite::memory:');

        $actual = Dialect::fromPdo($pdo);

        $this->assertInstanceOf($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_close_quote_char_is_bracket()
    {
        $dialect = new MssqlDialect;

        $actual = $dialect->getCloseQuoteChar();

        $this->assertEquals(']', $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_column_default_bool()
    {
        $dialect = new MssqlDialect;

        $column = new Column('active', 'TINYINT', 1);

        $column->defaultValue(true);

        $expect = '[active] BIT NOT NULL DEFAULT 1';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_column_default_int()
    {
        $dialect = new MssqlDialect;

        $column = new Column('age', 'INT');

        $column->defaultValue(0);

        $expect = '[age] INT NOT NULL DEFAULT 0';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_column_default_str()
    {
        $dialect = new MssqlDialect;

        $column = new Column('role', 'VARCHAR', 50);

        $column->defaultValue('admin');

        $expect = '[role] VARCHAR(50) NOT NULL DEFAULT \'admin\'';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_column_unique()
    {
        $dialect = new MssqlDialect;

        $column = new Column('email', 'VARCHAR', 255);

        $column->unique();

        $expect = '[email] VARCHAR(255) NOT NULL UNIQUE';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_column_uses_identity()
    {
        $dialect = new MssqlDialect;

        $column = new Column('id', 'INT');

        $column->autoIncrement();

        $expect = '[id] INT IDENTITY(1,1) NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_column_with_length()
    {
        $dialect = new MssqlDialect;

        $column = new Column('name', 'VARCHAR', 100);

        $expect = '[name] VARCHAR(100) NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_dialect_does_not_quote_star()
    {
        $dialect = new MssqlDialect;

        $expect = '*';

        $actual = $dialect->quote('*');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_dialect_has_limit()
    {
        $dialect = new MssqlDialect;

        $expect = ' OFFSET 5 ROWS FETCH NEXT 10 ROWS ONLY';

        $actual = $dialect->toLimit(10, 5);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_dialect_has_name()
    {
        $dialect = new MssqlDialect;

        $this->assertEquals('mssql', $dialect->getName());
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_dialect_quotes_alias()
    {
        $dialect = new MssqlDialect;

        $expect = '[users] [u]';

        $actual = $dialect->quote('users u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_dialect_quotes_bracket()
    {
        $dialect = new MssqlDialect;

        $expect = '[users]';

        $actual = $dialect->quote('users');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_dialect_quotes_qualified()
    {
        $dialect = new MssqlDialect;

        $expect = '[u].[name]';

        $actual = $dialect->quote('u.name');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_maps_boolean_to_bit()
    {
        $dialect = new MssqlDialect;

        $column = new Column('is_active', 'TINYINT', 1);

        $expect = '[is_active] BIT NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_maps_tinyint_to_smallint()
    {
        $dialect = new MssqlDialect;

        $column = new Column('count', 'TINYINT');

        $expect = '[count] SMALLINT NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_quote_char_is_bracket()
    {
        $dialect = new MssqlDialect;

        $actual = $dialect->getOpenQuoteChar();

        $this->assertEquals('[', $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_quote_handles_extra_spaces()
    {
        $dialect = new MssqlDialect;

        $expect = '[users]  [u]';

        $actual = $dialect->quote('users  u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_quote_handles_lowercase_as()
    {
        $dialect = new MssqlDialect;

        $expect = '[users] as [u]';

        $actual = $dialect->quote('users as u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_quotes_as_alias()
    {
        $dialect = new MssqlDialect;

        $expect = '[users] AS [u]';

        $actual = $dialect->quote('users AS u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_skips_bracketed()
    {
        $dialect = new MssqlDialect;

        $expect = '[table]';

        $actual = $dialect->quote('[table]');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_skips_function()
    {
        $dialect = new MssqlDialect;

        $expect = 'COUNT(*)';

        $actual = $dialect->quote('COUNT(*)');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_skips_number()
    {
        $dialect = new MssqlDialect;

        $expect = '1';

        $actual = $dialect->quote('1');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mssql_supports_right_join()
    {
        $dialect = new MssqlDialect;

        $this->assertTrue($dialect->canRightJoin());
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_column_uses_autoincrement()
    {
        $dialect = new MysqlDialect;

        $column = new Column('id', 'INT');

        $column->autoIncrement();

        $expect = '`id` INT NOT NULL AUTO_INCREMENT';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_dialect_does_not_quote_number()
    {
        $dialect = new MysqlDialect;

        $expect = '1';

        $actual = $dialect->quote('1');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_dialect_does_not_quote_star()
    {
        $dialect = new MysqlDialect;

        $expect = '`u`.*';

        $actual = $dialect->quote('u.*');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_dialect_has_limit()
    {
        $dialect = new MysqlDialect;

        $expect = ' LIMIT 10, 5';

        $actual = $dialect->toLimit(10, 5);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_dialect_has_name()
    {
        $dialect = new MysqlDialect;

        $this->assertEquals('mysql', $dialect->getName());
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_dialect_quotes_alias()
    {
        $dialect = new MysqlDialect;

        $expect = '`users` `u`';

        $actual = $dialect->quote('users u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_dialect_quotes_backtick()
    {
        $dialect = new MysqlDialect;

        $expect = '`users`';

        $actual = $dialect->quote('users');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_dialect_quotes_qualified()
    {
        $dialect = new MysqlDialect;

        $expect = '`u`.`name`';

        $actual = $dialect->quote('u.name');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_quote_handles_extra_spaces()
    {
        $dialect = new MysqlDialect;

        $expect = '`users`  `u`';

        $actual = $dialect->quote('users  u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_quote_handles_lowercase_as()
    {
        $dialect = new MysqlDialect;

        $expect = '`users` as `u`';

        $actual = $dialect->quote('users as u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_quotes_as_alias()
    {
        $dialect = new MysqlDialect;

        $expect = '`users` AS `u`';

        $actual = $dialect->quote('users AS u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_skips_function()
    {
        $dialect = new MysqlDialect;

        $expect = 'COUNT(*)';

        $actual = $dialect->quote('COUNT(*)');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_skips_quoted()
    {
        $dialect = new MysqlDialect;

        $expect = '`table`';

        $actual = $dialect->quote('`table`');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_mysql_supports_right_join()
    {
        $dialect = new MysqlDialect;

        $this->assertTrue($dialect->canRightJoin());
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_column_default_int()
    {
        $dialect = new PgsqlDialect;

        $column = new Column('age', 'INT');

        $column->defaultValue(0);

        $expect = '"age" INT NOT NULL DEFAULT 0';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_column_default_str()
    {
        $dialect = new PgsqlDialect;

        $column = new Column('role', 'VARCHAR', 50);

        $column->defaultValue('admin');

        $expect = '"role" VARCHAR(50) NOT NULL DEFAULT \'admin\'';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_column_unique()
    {
        $dialect = new PgsqlDialect;

        $column = new Column('email', 'VARCHAR', 255);

        $column->unique();

        $expect = '"email" VARCHAR(255) NOT NULL UNIQUE';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_column_uses_serial()
    {
        $dialect = new PgsqlDialect;

        $column = new Column('id', 'INT');

        $column->autoIncrement();

        $expect = '"id" SERIAL NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_dialect_does_not_quote_number()
    {
        $dialect = new PgsqlDialect;

        $expect = '1';

        $actual = $dialect->quote('1');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_dialect_does_not_quote_star()
    {
        $dialect = new PgsqlDialect;

        $expect = '*';

        $actual = $dialect->quote('*');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_dialect_has_limit()
    {
        $dialect = new PgsqlDialect;

        $expect = ' LIMIT 10 OFFSET 5';

        $actual = $dialect->toLimit(10, 5);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_dialect_has_name()
    {
        $dialect = new PgsqlDialect;

        $this->assertEquals('pgsql', $dialect->getName());
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_dialect_quotes_alias()
    {
        $dialect = new PgsqlDialect;

        $expect = '"users" "u"';

        $actual = $dialect->quote('users u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_dialect_quotes_double()
    {
        $dialect = new PgsqlDialect;

        $expect = '"users"';

        $actual = $dialect->quote('users');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_dialect_quotes_qualified()
    {
        $dialect = new PgsqlDialect;

        $expect = '"u"."name"';

        $actual = $dialect->quote('u.name');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_maps_boolean_to_boolean()
    {
        $dialect = new PgsqlDialect;

        $column = new Column('is_active', 'TINYINT', 1);

        $expect = '"is_active" BOOLEAN NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_maps_datetime_to_timestamp()
    {
        $dialect = new PgsqlDialect;

        $column = new Column('created', 'DATETIME');

        $expect = '"created" TIMESTAMP NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_maps_tinyint_to_smallint()
    {
        $dialect = new PgsqlDialect;

        $column = new Column('count', 'TINYINT');

        $expect = '"count" SMALLINT NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_quote_char_is_double()
    {
        $dialect = new PgsqlDialect;

        $actual = $dialect->getOpenQuoteChar();

        $this->assertEquals('"', $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_quotes_as_alias()
    {
        $dialect = new PgsqlDialect;

        $expect = '"users" AS "u"';

        $actual = $dialect->quote('users AS u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_skips_function()
    {
        $dialect = new PgsqlDialect;

        $expect = 'COUNT(*)';

        $actual = $dialect->quote('COUNT(*)');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_skips_quoted()
    {
        $dialect = new PgsqlDialect;

        $expect = '"table"';

        $actual = $dialect->quote('"table"');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_pgsql_supports_right_join()
    {
        $dialect = new PgsqlDialect;

        $this->assertTrue($dialect->canRightJoin());
    }

    /**
     * @return void
     */
    public function test_passed_if_query_can_set_dialect()
    {
        $expect = 'Rougin\Ezekiel\Dialect\PgsqlDialect';

        $query = new Query;

        $query->setDialect(new PgsqlDialect);

        $actual = $query->getDialect();

        $this->assertInstanceOf($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_query_uses_pgsql_dialect_limit()
    {
        $query = new Query;

        $query->setDialect(new PgsqlDialect);

        $sql = $query->select('*')->from('users')->limit(10, 5)->toSql();

        $expect = 'SELECT * FROM "users" LIMIT 10 OFFSET 5';

        $this->assertEquals($expect, $sql);
    }

    /**
     * @return void
     */
    public function test_passed_if_query_with_no_dialect_returns_mysql()
    {
        $expect = 'Rougin\Ezekiel\Dialect\MysqlDialect';

        $query = new Query;

        $actual = $query->getDialect();

        $this->assertInstanceOf($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_column_default_bool()
    {
        $dialect = new SqliteDialect;

        $column = new Column('active', 'TINYINT', 1);

        $column->defaultValue(true);

        $expect = '"active" INTEGER NOT NULL DEFAULT 1';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_column_default_int()
    {
        $dialect = new SqliteDialect;

        $column = new Column('age', 'INT');

        $column->defaultValue(0);

        $expect = '"age" INTEGER NOT NULL DEFAULT 0';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_column_default_str()
    {
        $dialect = new SqliteDialect;

        $column = new Column('role', 'VARCHAR', 50);

        $column->defaultValue('admin');

        $expect = '"role" VARCHAR(50) NOT NULL DEFAULT \'admin\'';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_column_unique()
    {
        $dialect = new SqliteDialect;

        $column = new Column('email', 'VARCHAR', 255);

        $column->unique();

        $expect = '"email" VARCHAR(255) NOT NULL UNIQUE';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_column_uses_autoincrement()
    {
        $dialect = new SqliteDialect;

        $column = new Column('id', 'INT');

        $column->autoIncrement();

        $expect = '"id" INTEGER NOT NULL AUTOINCREMENT';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_dialect_does_not_quote_star()
    {
        $dialect = new SqliteDialect;

        $expect = '*';

        $actual = $dialect->quote('*');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_dialect_has_limit()
    {
        $dialect = new SqliteDialect;

        $expect = ' LIMIT 10 OFFSET 5';

        $actual = $dialect->toLimit(10, 5);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_dialect_has_name()
    {
        $dialect = new SqliteDialect;

        $this->assertEquals('sqlite', $dialect->getName());
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_dialect_quotes_alias()
    {
        $dialect = new SqliteDialect;

        $expect = '"users" "u"';

        $actual = $dialect->quote('users u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_dialect_quotes_double()
    {
        $dialect = new SqliteDialect;

        $expect = '"users"';

        $actual = $dialect->quote('users');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_dialect_quotes_qualified()
    {
        $dialect = new SqliteDialect;

        $expect = '"u"."name"';

        $actual = $dialect->quote('u.name');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_dialect_skips_number()
    {
        $dialect = new SqliteDialect;

        $expect = '1';

        $actual = $dialect->quote('1');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_does_not_support_right_join()
    {
        $dialect = new SqliteDialect;

        $this->assertFalse($dialect->canRightJoin());
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_maps_datetime_to_text()
    {
        $dialect = new SqliteDialect;

        $column = new Column('created', 'DATETIME');

        $expect = '"created" TEXT NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_maps_tinyint_to_integer()
    {
        $dialect = new SqliteDialect;

        $column = new Column('count', 'TINYINT');

        $expect = '"count" INTEGER NOT NULL';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_primary_before_autoincr()
    {
        $dialect = new SqliteDialect;

        $column = new Column('id', 'INT');

        $column->autoIncrement();

        $column->primary();

        $expect = '"id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT';

        $actual = $dialect->toColumn($column);

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_quote_char_is_double()
    {
        $dialect = new SqliteDialect;

        $actual = $dialect->getOpenQuoteChar();

        $this->assertEquals('"', $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_quotes_as_alias()
    {
        $dialect = new SqliteDialect;

        $expect = '"users" AS "u"';

        $actual = $dialect->quote('users AS u');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_right_join_falls_back()
    {
        $query = new Query;

        $query->setDialect(new SqliteDialect);

        $sql = $query->select('u.*')->from('users u')
            ->rightJoin('profiles p')
            ->on('p.user_id', 'u.id')
            ->toSql();

        $expect = 'SELECT "u".* FROM "users" "u"';

        $expect .= ' LEFT JOIN "profiles" "p" ON "p"."user_id" = "u"."id"';

        $this->assertEquals($expect, $sql);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_skips_function()
    {
        $dialect = new SqliteDialect;

        $expect = 'COUNT(*)';

        $actual = $dialect->quote('COUNT(*)');

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_sqlite_skips_quoted()
    {
        $dialect = new SqliteDialect;

        $expect = '"table"';

        $actual = $dialect->quote('"table"');

        $this->assertEquals($expect, $actual);
    }
}

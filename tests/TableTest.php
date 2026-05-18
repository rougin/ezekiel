<?php

namespace Rougin\Ezekiel;

use Rougin\Ezekiel\Dialect\PgsqlDialect;
use Rougin\Ezekiel\Schema\Design;
use Rougin\Ezekiel\Schema\Table;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class TableTest extends Testcase
{
    /**
     * @return void
     */
    public function test_passed_if_all_column_types_compile()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`total` BIGINT NOT NULL, ';
        $sql .= '`is_active` TINYINT(1) NOT NULL, ';
        $sql .= '`birthday` DATE NOT NULL, ';
        $sql .= '`last_login` DATETIME NOT NULL, ';
        $sql .= '`price` DECIMAL(8, 2) NOT NULL, ';
        $sql .= '`rating` FLOAT NOT NULL, ';
        $sql .= '`count` TINYINT NOT NULL';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->bigInteger('total');

            $d->boolean('is_active');

            $d->date('birthday');

            $d->dateTime('last_login');

            $d->decimal('price');

            $d->float('rating');

            $d->tinyInteger('count');
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_column_default_is_bool()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`is_admin` TINYINT(1) NOT NULL DEFAULT 1';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->increments('id');

            $d->boolean('is_admin')->defaultValue(true);
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_column_default_is_string()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`role` VARCHAR(50) NOT NULL DEFAULT \'guest\'';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->increments('id');

            $d->string('role', 50)->defaultValue('guest');
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_index_and_primary_compile()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`name` VARCHAR(255) NOT NULL, ';
        $sql .= 'INDEX (`name`), ';
        $sql .= 'PRIMARY KEY (`name`)';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->string('name');

            $d->index('name');

            $d->primary('name');
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_alters()
    {
        $sql = 'ALTER TABLE `users` ADD `bio` TEXT NOT NULL';

        $table = new Table;

        $table->table('users', function (Design $d)
        {
            $d->text('bio');
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_drops()
    {
        $expect = 'DROP TABLE `users`';

        $table = new Table;

        $table->drop('users');

        $actual = $table->toSql();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_drops_if_exists()
    {
        $expect = 'DROP TABLE IF EXISTS `users`';

        $table = new Table;

        $table->dropIfExists('users');

        $actual = $table->toSql();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_has_increments()
    {
        $expect = 'CREATE TABLE `users` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY)';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->increments('id');
        });

        $actual = $table->toSql();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_has_index()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`email` VARCHAR(255) NOT NULL, ';
        $sql .= 'UNIQUE (`email`)';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->increments('id');

            $d->string('email');

            $d->unique('email');
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_has_modifiers()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`bio` TEXT, ';
        $sql .= '`age` INT NOT NULL DEFAULT 0, ';
        $sql .= '`email` VARCHAR(255) NOT NULL UNIQUE';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->increments('id');

            $d->text('bio')->nullable();

            $d->integer('age')->defaultValue(0);

            $d->string('email')->unique();
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_has_multiple_columns()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`name` VARCHAR(100) NOT NULL, ';
        $sql .= '`email` VARCHAR(255) NOT NULL';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->increments('id');

            $d->string('name', 100);

            $d->string('email');
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_has_soft_deletes()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`deleted_at` TIMESTAMP';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->increments('id');

            $d->softDeletes();
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_has_timestamps()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`created_at` TIMESTAMP, ';
        $sql .= '`updated_at` TIMESTAMP';
        $sql .= ')';

        $table = new Table;

        $table->create('users', function (Design $d)
        {
            $d->increments('id');

            $d->timestamps();
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_uses_pgsql_dialect()
    {
        $sql = 'CREATE TABLE "users" ("id" INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '"name" VARCHAR(100) NOT NULL)';

        $table = new Table(new PgsqlDialect);

        $table->create('users', function (Design $d)
        {
            $d->increments('id');

            $d->string('name', 100);
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }
}

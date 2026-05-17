<?php

namespace Rougin\Ezekiel;

use Rougin\Ezekiel\Dialect\PgsqlDialect;

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
    public function test_create_table_with_increments()
    {
        $expect = 'CREATE TABLE `users` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY)';

        $table = new Schema\Table;

        $table->create('users', function (Schema\Design $d)
        {
            $d->increments('id');
        });

        $actual = $table->toSql();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_create_table_with_multiple_columns()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`name` VARCHAR(100) NOT NULL, ';
        $sql .= '`email` VARCHAR(255) NOT NULL';
        $sql .= ')';

        $table = new Schema\Table;

        $table->create('users', function (Schema\Design $d)
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
    public function test_create_table_with_modifiers()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`bio` TEXT, ';
        $sql .= '`age` INT NOT NULL DEFAULT 0, ';
        $sql .= '`email` VARCHAR(255) NOT NULL UNIQUE';
        $sql .= ')';

        $table = new Schema\Table;

        $table->create('users', function (Schema\Design $d)
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
    public function test_create_table_with_timestamps()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`created_at` TIMESTAMP, ';
        $sql .= '`updated_at` TIMESTAMP';
        $sql .= ')';

        $table = new Schema\Table;

        $table->create('users', function (Schema\Design $d)
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
    public function test_create_table_with_soft_deletes()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`deleted_at` TIMESTAMP';
        $sql .= ')';

        $table = new Schema\Table;

        $table->create('users', function (Schema\Design $d)
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
    public function test_create_table_with_index()
    {
        $sql = 'CREATE TABLE `users` (';
        $sql .= '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '`email` VARCHAR(255) NOT NULL, ';
        $sql .= 'UNIQUE (`email`)';
        $sql .= ')';

        $table = new Schema\Table;

        $table->create('users', function (Schema\Design $d)
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
    public function test_drop_table()
    {
        $expect = 'DROP TABLE `users`';

        $table = new Schema\Table;

        $table->drop('users');

        $actual = $table->toSql();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_drop_if_exists()
    {
        $expect = 'DROP TABLE IF EXISTS `users`';

        $table = new Schema\Table;

        $table->dropIfExists('users');

        $actual = $table->toSql();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_create_table_with_pgsql_dialect()
    {
        $sql = 'CREATE TABLE "users" ("id" INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
        $sql .= '"name" VARCHAR(100) NOT NULL)';

        $table = new Schema\Table(new PgsqlDialect);

        $table->create('users', function (Schema\Design $d)
        {
            $d->increments('id');

            $d->string('name', 100);
        });

        $actual = $table->toSql();

        $this->assertEquals($sql, $actual);
    }
}

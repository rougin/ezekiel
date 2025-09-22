<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class WhereTest extends Testcase
{
    /**
     * @return void
     */
    public function test_equals()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "id" = ?';

        $query->select('*')->from('users');
        $query->where('id')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_greater_than()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "id" > ?';

        $query->select('*')->from('users');
        $query->where('id')->greaterThan(1);

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_greater_than_or_equal_to()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "id" >= ?';

        $query->select('*')->from('users');
        $query->where('id')->greaterThanOrEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_in()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "id" IN (?, ?, ?)';

        $query->select('*')->from('users');
        $query->where('id')->in(array(1, 2, 3));

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_is_false()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "active" = ?';

        $query->select('*')->from('users');
        $query->where('active')->isFalse();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_is_not_null()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "name" IS NOT NULL';

        $query->select('*')->from('users');
        $query->where('name')->isNotNull();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_is_null()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "name" IS NULL';

        $query->select('*')->from('users');
        $query->where('name')->isNull();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_is_true()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "active" = ?';

        $query->select('*')->from('users');
        $query->where('active')->isTrue();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_less_than()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "id" < ?';

        $query->select('*')->from('users');
        $query->where('id')->lessThan(1);

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_less_than_or_equal_to()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "id" <= ?';

        $query->select('*')->from('users');
        $query->where('id')->lessThanOrEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_like()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "name" LIKE ?';

        $query->select('*')->from('users');
        $query->where('name')->like('%Ezekiel%');

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_not_equal_to()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "id" != ?';

        $query->select('*')->from('users');
        $query->where('id')->notEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_not_in()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "id" NOT IN (?, ?, ?)';

        $query->select('*')->from('users');
        $query->where('id')->notIn(array(1, 2, 3));

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_not_like()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "name" NOT LIKE ?';

        $query->select('*')->from('users');
        $query->where('name')->notLike('%Ezekiel%');

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_with_and_or()
    {
        $query = new Query;

        $expected = 'SELECT * FROM "users" WHERE "name" = ? AND "age" > ? OR "status" = ?';

        $query->select('*')->from('users');
        $query->where('name')->equals('%Ezekiel%');
        $query->andWhere('age')->greaterThan(5);
        $query->orWhere('status')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }
}

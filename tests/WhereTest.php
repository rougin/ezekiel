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

        $sql = 'SELECT * FROM users WHERE id = ?';

        $query->select('*')->from('users')
            ->where('id')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_greater_than()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE id > ?';

        $query->select('*')->from('users')
            ->where('id')->greaterThan(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_greater_than_or_equal_to()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE id >= ?';

        $query->select('*')->from('users')
            ->where('id')->greaterThanOrEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_in()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE id IN (?, ?, ?)';

        $query->select('*')->from('users')
            ->where('id')->in(array(1, 2, 3));

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_is_false()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE active = ?';

        $query->select('*')->from('users')
            ->where('active')->isFalse();

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_is_not_null()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE name IS NOT NULL';

        $query->select('*')->from('users')
            ->where('name')->isNotNull();

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_is_null()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE name IS NULL';

        $query->select('*')->from('users')
            ->where('name')->isNull();

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_is_true()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE active = ?';

        $query->select('*')->from('users')
            ->where('active')->isTrue();

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_less_than()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE id < ?';

        $query->select('*')->from('users')
            ->where('id')->lessThan(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_less_than_or_equal_to()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE id <= ?';

        $query->select('*')->from('users')
            ->where('id')->lessThanOrEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_like()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE name LIKE ?';

        $query->select('*')->from('users')
            ->where('name')->like('%Ezekiel%');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_not_equal_to()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE id != ?';

        $query->select('*')->from('users')
            ->where('id')->notEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_not_in()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE id NOT IN (?, ?, ?)';

        $query->select('*')->from('users')
            ->where('id')->notIn(array(1, 2, 3));

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_not_like()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE name NOT LIKE ?';

        $query->select('*')->from('users')
            ->where('name')->notLike('%Ezekiel%');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_with_and_or()
    {
        $query = new Query;

        $sql = 'SELECT * FROM users WHERE name = ? AND age > ? OR status = ?';

        $query->select('*')->from('users')
            ->where('name')->equals('%Ezekiel%')
            ->andWhere('age')->greaterThan(5)
            ->orWhere('status')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }
}

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
    public function test_passed_if_and_where_group_exists()
    {
        // Set expected SQL query and its attached data -----
        $sql = 'SELECT * FROM users ';

        $sql .= 'WHERE name = ? AND (age > ? OR status = ?)';

        $data = array('name' => 'Alice', 'age' => 5);

        $data['status'] = 1;
        // --------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('name')->equals('Alice');

        $query->andWhereGroup(function (Query $inner)
        {
            $inner->where('age')->greaterThan(5)
                ->orWhere('status')->equals(1);
        });

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_or_where_group_exists()
    {
        // Set expected SQL query and its attached data ----
        $sql = 'SELECT * FROM users ';

        $sql .= 'WHERE name = ? OR (age > ? OR status = ?)';

        $data = array('name' => 'Alice', 'age' => 5);

        $data['status'] = 1;
        // -------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('name')->equals('Alice');

        $query->orWhereGroup(function (Query $inner)
        {
            $inner->where('age')->greaterThan(5)
                ->orWhere('status')->equals(1);
        });

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_chains_and_or()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE name LIKE ?';

        $sql .= ' AND age > ? OR status = ?';

        $data = array('name' => '%Ezekiel%', 'age' => 5);

        $data['status'] = 1;
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('name')->like('%Ezekiel%')
            ->andWhere('age')->greaterThan(5)
            ->orWhere('status')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_group_is_callable()
    {
        // Set expected SQL query and its attached data ------------
        $sql = 'SELECT * FROM users ';
        $sql .= 'WHERE (name = ? OR status = ?)';

        $data = array('name' => 'Alice', 'status' => 1);
        // ---------------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->whereGroup(function (Query $inner)
            {
                $inner->where('name')->equals('Alice')
                    ->orWhere('status')->equals(1);
            });

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_group_skips_non_where()
    {
        // Set expected SQL query --------------------
        $sql = 'SELECT * FROM users WHERE (name = ?)';
        // -------------------------------------------

        $query = new Query;

        $query->select('*')->from('users')
            ->whereGroup(function (Query $inner)
            {
                $inner->where('name')->equals('Alice');
                $inner->innerJoin('orders')->on('u.id', 'o.user_id');
            });

        // Check if the actual SQL query matched ---
        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_group_with_and_chain()
    {
        // Set expected SQL query and its attached data ------------
        $sql = 'SELECT * FROM users ';
        $sql .= 'WHERE (name = ? OR age > ?) AND status = ?';

        $data = array('name' => 'Alice', 'age' => 5);
        $data['status'] = 1;
        // ---------------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users');

        $query->whereGroup(function (Query $inner)
        {
            $inner->where('name')->equals('Alice')
                ->orWhere('age')->greaterThan(5);
        });

        $query->andWhere('status')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_equals()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE id = ?';

        $data = array('id' => 1);
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_greater_than()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE id > ?';

        $data = array('id' => 1);
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->greaterThan(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_gte()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE id >= ?';

        $data = array('id' => 1);
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->greaterThanOrEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_in()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE id IN (?, ?, ?)';

        $data = array('id' => array(1, 3, 4));
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->in(array(1, 3, 4));

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_is_false()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE active = ?';

        $data = array('active' => false);
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('active')->isFalse();

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_is_not_null()
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
    public function test_passed_if_where_uses_is_null()
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
    public function test_passed_if_where_uses_is_true()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE active = ?';

        $data = array('active' => true);
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('active')->isTrue();

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_less_than()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE id < ?';

        $data = array('id' => 1);
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->lessThan(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_like()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE name LIKE ?';

        $data = array('name' => '%Ezekiel%');
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('name')->like('%Ezekiel%');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_lte()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE id <= ?';

        $data = array('id' => 1);
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->lessThanOrEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_not_equal()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE id != ?';

        $data = array('id' => 1);
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->notEqualTo(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_not_in()
    {
        // Set expected SQL query and its attached data -------
        $sql = 'SELECT * FROM users WHERE id NOT IN (?, ?, ?)';

        $data = array('id' => array(1, 2, 3));
        // ----------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->notIn(array(1, 2, 3));

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_where_uses_not_like()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users WHERE name NOT LIKE ?';

        $data = array('name' => '%Ezekiel%');
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('name')->notLike('%Ezekiel%');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }
}

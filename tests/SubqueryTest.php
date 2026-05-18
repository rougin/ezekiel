<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SubqueryTest extends Testcase
{
    /**
     * @return void
     */
    public function test_passed_if_from_uses_subquery()
    {
        $sql = 'SELECT * FROM (SELECT * FROM `users` WHERE `active` = ?) active_users';

        $expect = array('active' => 1);

        $sub = new Query;

        $sub->select('*')->from('users')
            ->where('active')->equals(1);

        $query = new Query;

        $query->select('*')->from($sub, 'active_users');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);

        $actual = $query->getBinds();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_subquery_binds_merge_with_parent()
    {
        $sql = 'SELECT * FROM `users` WHERE `id` IN (SELECT `user_id` FROM `posts` WHERE `status` = ?) AND `active` = ?';

        $expect = array('status' => 1, 'active' => 1);

        $sub = new Query;

        $sub->select('user_id')->from('posts')
            ->where('status')->equals(1);

        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->in($sub)
            ->andWhere('active')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);

        $actual = $query->getBinds();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_equals_uses_subquery()
    {
        $sql = 'SELECT * FROM `users` WHERE `age` = (SELECT MAX(age) FROM `users`)';

        $sub = new Query;

        $sub->select('MAX(age)')->from('users');

        $query = new Query;

        $query->select('*')->from('users')
            ->where('age')->equals($sub);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_greater_than_uses_subquery()
    {
        $sql = 'SELECT * FROM `users` WHERE `age` > (SELECT AVG(age) FROM `users`)';

        $sub = new Query;

        $sub->select('AVG(age)')->from('users');

        $query = new Query;

        $query->select('*')->from('users')
            ->where('age')->greaterThan($sub);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_in_uses_subquery()
    {
        $sql = 'SELECT * FROM `users` WHERE `id` IN (SELECT `user_id` FROM `posts` WHERE `status` = ?)';

        $expect = array('status' => 1);

        $sub = new Query;

        $sub->select('user_id')->from('posts')
            ->where('status')->equals(1);

        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->in($sub);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);

        $actual = $query->getBinds();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_not_in_uses_subquery()
    {
        $sql = 'SELECT * FROM `users` WHERE `id` NOT IN (SELECT `user_id` FROM `posts` WHERE `status` = ?)';

        $expect = array('status' => 1);

        $sub = new Query;

        $sub->select('user_id')->from('posts')
            ->where('status')->equals(1);

        $query = new Query;

        $query->select('*')->from('users')
            ->where('id')->notIn($sub);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);

        $actual = $query->getBinds();

        $this->assertEquals($expect, $actual);
    }
}

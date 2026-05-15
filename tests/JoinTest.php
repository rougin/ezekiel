<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class JoinTest extends Testcase
{
    /**
     * @return void
     */
    public function test_passed_if_join_uses_inner()
    {
        // Set expected SQL query ------------------------
        $sql = 'SELECT u.* FROM users u';

        $sql .= ' INNER JOIN posts p ON p.user_id = u.id';
        // -----------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->innerJoin('posts p')
            ->on('p.user_id', 'u.id');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_join_uses_left()
    {
        // Set expected SQL query -----------------------
        $sql = 'SELECT u.* FROM users u';

        $sql .= ' LEFT JOIN posts p ON p.user_id = u.id';
        // ----------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->leftJoin('posts p')
            ->on('p.user_id', 'u.id');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_join_uses_right()
    {
        // Set expected SQL query ------------------------
        $sql = 'SELECT u.* FROM users u';

        $sql .= ' RIGHT JOIN posts p ON p.user_id = u.id';
        // -----------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->rightJoin('posts p')
            ->on('p.user_id', 'u.id');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------
    }
}

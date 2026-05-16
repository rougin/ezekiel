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
        $expect = 'SELECT `u`.* FROM `users` `u`';

        $expect .= ' INNER JOIN `posts` `p` ON `p`.`user_id` = `u`.`id`';

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->innerJoin('posts p')
            ->on('p.user_id', 'u.id');

        $actual = $query->toSql();

        $this->assertEquals($expect, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_join_uses_left()
    {
        $expect = 'SELECT `u`.* FROM `users` `u`';

        $expect .= ' LEFT JOIN `posts` `p` ON `p`.`user_id` = `u`.`id`';

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->leftJoin('posts p')
            ->on('p.user_id', 'u.id');

        $actual = $query->toSql();

        $this->assertEquals($expect, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_join_uses_right()
    {
        $expect = 'SELECT `u`.* FROM `users` `u`';

        $expect .= ' RIGHT JOIN `posts` `p` ON `p`.`user_id` = `u`.`id`';

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->rightJoin('posts p')
            ->on('p.user_id', 'u.id');

        $actual = $query->toSql();

        $this->assertEquals($expect, $actual);
        // -----------------------------------------
    }
}

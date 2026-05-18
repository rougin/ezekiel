<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class InsertTest extends Testcase
{
    /**
     * @return void
     */
    public function test_passed_if_insert_uses_batch_values()
    {
        $sql = 'INSERT INTO `users` (`name`, `age`) VALUES (?, ?), (?, ?)';

        $expect = array('Alice', 25, 'Bob', 30);

        $query = new Query;

        /** @var array<int, array<string, mixed>> */
        $values = array(
            array('name' => 'Alice', 'age' => 25),
            array('name' => 'Bob', 'age' => 30),
        );

        $query->insertInto('users')->values($values);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);

        $actual = $query->getBinds();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_insert_uses_values()
    {
        $sql = 'INSERT INTO `users` (`name`, `age`) VALUES (?, ?)';

        $expect = array('name' => 'Royce');

        $expect['age'] = 20;

        // Check if the actual SQL query matched ----
        $query = new Query;

        $query->insertInto('users')->values($expect);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // ------------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($expect, $actual);
        // ----------------------------------------
    }
}

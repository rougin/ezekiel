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

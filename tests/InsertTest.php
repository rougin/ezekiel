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
    public function test_with_values()
    {
        // Set expected SQL query and its attached data -----
        $sql = 'INSERT INTO users (name, age) VALUES (?, ?)';

        $data = array('name' => 'Royce');

        $data['age'] = 20;
        // --------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->insertInto('users')->values($data);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }
}

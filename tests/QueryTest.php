<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class QueryTest extends Testcase
{
    /**
     * @return void
     */
    public function test_with_group_by()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'SELECT * FROM users GROUP BY name, age';
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->groupBy(array('name', 'age'));

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_with_having()
    {
        // Set expected SQL query and its attached data ------------
        $sql = 'SELECT * FROM users GROUP BY name, age, date';

        $sql = $sql . ' HAVING name = ? AND age > ? OR date = ?';

        $data = array('name' => 'Royce', 'age' => 5, 'date' => 100);
        // ---------------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->groupBy(array('name', 'age', 'date'))
            ->having('name')->equals('Royce')
            ->andHaving('age')->greaterThan(5)
            ->orHaving('date')->equals(100);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }
}

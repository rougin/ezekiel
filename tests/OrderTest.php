<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class OrderTest extends Testcase
{
    /**
     * @return void
     */
    public function test_asc()
    {
        // Set expected SQL query --------------------------
        $expected = 'SELECT * FROM users ORDER BY name ASC';
        // -------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->orderBy('name')->asc();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_desc()
    {
        // Set expected SQL query ---------------------------
        $expected = 'SELECT * FROM users ORDER BY name DESC';
        // --------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->orderBy('name')->desc();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_multiple_sort()
    {
        // Set expected SQL query ------------------------------------
        $expected = 'SELECT * FROM users ORDER BY name DESC, age ASC';
        // -----------------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->orderBy('name')->desc()
            ->andOrderBy('age')->asc();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
        // -----------------------------------------
    }
}

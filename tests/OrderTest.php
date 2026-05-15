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
    public function test_passed_if_order_by_has_multiple_fields()
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

    /**
     * @return void
     */
    public function test_passed_if_order_by_uses_asc()
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
    public function test_passed_if_order_by_uses_desc()
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
}

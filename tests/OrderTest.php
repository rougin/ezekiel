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
        $expected = 'SELECT * FROM users ORDER BY name ASC';

        $query = new Query;

        $query->select('*')->from('users')
            ->orderBy('name')->asc();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_desc()
    {
        $expected = 'SELECT * FROM users ORDER BY name DESC';

        $query = new Query;

        $query->select('*')->from('users')
            ->orderBy('name')->desc();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_multiple_sort()
    {
        $expected = 'SELECT * FROM users ORDER BY name DESC, age ASC';

        $query = new Query;

        $query->select('*')->from('users')
            ->orderBy('name')->desc()
            ->andOrderBy('age')->asc();

        $actual = $query->toSql();

        $this->assertEquals($expected, $actual);
    }
}

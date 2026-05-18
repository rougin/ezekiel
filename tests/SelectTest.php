<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SelectTest extends Testcase
{
    /**
     * @return void
     */
    public function test_passed_if_select_distinct_uses_multi_fields()
    {
        $expect = 'SELECT DISTINCT `name`, `age` FROM `users`';

        $query = new Query;

        $query->select(array('name', 'age'))->distinct()->from('users');

        $actual = $query->toSql();

        $this->assertEquals($expect, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_select_has_distinct()
    {
        $expect = 'SELECT DISTINCT `name` FROM `users`';

        $query = new Query;

        $query->select('name')->distinct()->from('users');

        $actual = $query->toSql();

        $this->assertEquals($expect, $actual);
    }
}

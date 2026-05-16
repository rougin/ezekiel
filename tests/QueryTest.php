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
    public function test_passed_if_delete_from_has_where()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'DELETE FROM `users` WHERE `name` = ?';

        $expect = array('name' => 'Royce');
        // ------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->deleteFrom('users')
            ->where('name')->equals('Royce');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($expect, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_select_has_group_by()
    {
        $expect = 'SELECT * FROM `users` GROUP BY name';

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->groupBy('name');

        $actual = $query->toSql();

        $this->assertEquals($expect, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_select_has_having()
    {
        $sql = 'SELECT * FROM `users` GROUP BY name, age, date';

        $sql .= ' HAVING `name` = ? AND `age` > ? OR `date` = ?';

        $expect = array('name' => 'Royce', 'age' => 5, 'date' => 100);

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

        $this->assertEquals($expect, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_select_has_limit()
    {
        $expect = 'SELECT * FROM `users` LIMIT 100, 0';

        // Check if the actual SQL query matched ------
        $query = new Query;

        $query->select('*')->from('users')->limit(100);

        $actual = $query->toSql();

        $this->assertEquals($expect, $actual);
        // --------------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_select_uses_alias()
    {
        $expect = 'SELECT `u`.* FROM `users` `u` WHERE `u`.`name` = ?';

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->where('u.name')->equals('Royce');

        $actual = $query->toSql();

        $this->assertEquals($expect, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_snake_case_method_is_callable()
    {
        $sql = 'SELECT * FROM `users` WHERE `name` = ?';

        $expect = array('name' => 'Royce');

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('*')->from('users')
            ->where('name')->equals('Royce');

        $actual = $query->to_sql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->get_binds();

        $this->assertEquals($expect, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_snake_case_rejects_invalid_method()
    {
        $this->doExpectException('BadMethodCallException');

        $query = new Query;

        $query->__call('invalid_method', array());
    }
}

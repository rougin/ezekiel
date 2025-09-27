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
    public function test_with_alias()
    {
        // Set expected SQL query ------------------------
        $sql = 'SELECT u.* FROM users u WHERE u.name = ?';
        // -----------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->where('u.name')->equals('Royce');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_with_delete()
    {
        // Set expected SQL query and its attached data ---
        $sql = 'DELETE FROM users WHERE name = ?';

        $data = array('name' => 'Royce');
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

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_with_group_by()
    {
        // Set expected SQL query -----------------
        $sql = 'SELECT * FROM users GROUP BY name';
        // ----------------------------------------

        // Check if the actual SQL query matched -----------
        $query = new Query;

        $query->select('*')->from('users')->groupBy('name');

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -------------------------------------------------
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

    /**
     * @return void
     */
    public function test_with_limit()
    {
        // Set expected SQL query ----------------
        $sql = 'SELECT * FROM users LIMIT 100, 0';
        // ---------------------------------------

        // Check if the actual SQL query matched ------
        $query = new Query;

        $query->select('*')->from('users')->limit(100);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // --------------------------------------------
    }
}

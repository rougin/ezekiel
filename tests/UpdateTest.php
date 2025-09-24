<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class UpdateTest extends Testcase
{
    /**
     * @return void
     */
    public function test_with_set()
    {
        // Set expected SQL query and its attached data ---------
        $sql = 'UPDATE users SET name = ?, age = ? WHERE id = ?';

        $data = array('name' => 'Royce', 'age' => 30, 'id' => 1);
        // ------------------------------------------------------

        // Check if the actual SQL query matched ---
        $query = new Query;

        $query->update('users')
            ->set('name', 'Royce')
            ->set('age', 30)
            ->where('id')->equals(1);

        $actual = $query->toSql();

        $this->assertEquals($sql, $actual);
        // -----------------------------------------

        // Check if the actual bindings matched ---
        $actual = $query->getBinds();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }
}

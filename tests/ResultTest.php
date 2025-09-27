<?php

namespace Rougin\Ezekiel;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class ResultTest extends Testcase
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @return void
     */
    public function test_with_correct_data()
    {
        // Define the expected data --------------------
        $data = array();

        $data[] = array('id' => 2, 'name' => 'Windsor');
        // ---------------------------------------------

        // Check if the actual results returned ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->where('u.name')->equals('Windsor');

        $result = new Result($this->pdo, $query);

        $actual = $result->toItems();

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        // Initialize the database ------------
        $driver = $_SERVER['DB_DRIVER'];

        $name = $_SERVER['DB_DATABASE'];

        $pdo = new \PDO($driver . ':' . $name);
        // ------------------------------------

        // Throw PDOException if an error occurs ---
        $attr = \PDO::ATTR_ERRMODE;

        $key = \PDO::ERRMODE_EXCEPTION;

        $pdo->setAttribute($attr, $key);
        // -----------------------------------------

        // Create the table and its initial data --------------------------
        $pdo->exec('CREATE TABLE users (id INTEGER, name TEXT)');

        $pdo->exec('INSERT INTO users (id, name) VALUES (2, \'Windsor\')');
        // ----------------------------------------------------------------

        $this->pdo = $pdo;
    }
}

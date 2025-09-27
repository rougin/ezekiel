<?php

namespace Rougin\Ezekiel;

use PDO;

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
     * Sets up the PDO connection.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->pdo = new PDO($_SERVER['DB_DRIVER'] . ':' . $_SERVER['DB_DATABASE']);

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec('CREATE TABLE users (id INTEGER, name TEXT)');

        $this->pdo->exec('INSERT INTO users (id, name) VALUES (2, \'Windsor\')');
    }

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
}

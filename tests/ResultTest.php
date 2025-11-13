<?php

namespace Rougin\Ezekiel;

use Rougin\Ezekiel\Fixture\Entities\User;
use Rougin\Ezekiel\Fixture\Results\UserResult;

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
    public function test_with_entities()
    {
        // Define the expected data --------
        $user = new User;

        $user->setId(2)->setName('Windsor');
        // ---------------------------------

        // Check if the actual results returned ---
        $query = new User;

        $query->select('id, name')->from('users')
            ->where('name')->equals('Windsor');

        $result = new UserResult($this->pdo);

        $items = $result->items($query);

        $expect = $user->getName();
        $actual = $items[0]->getName();
        $this->assertEquals($expect, $actual);

        $expect = $user->getId();
        $actual = $items[0]->getId();
        $this->assertEquals($expect, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_with_entity()
    {
        // Define the expected data --------
        $user = new User;

        $user->setId(2)->setName('Windsor');
        // ---------------------------------

        // Check if the actual results returned ---
        $query = new User;

        $query->select('id, name')->from('users')
            ->where('name')->equals('Windsor');

        $result = new UserResult($this->pdo);

        $item = $result->first($query);

        $expect = $user->getName();
        $actual = $item->getName();
        $this->assertEquals($expect, $actual);

        $expect = $user->getId();
        $actual = $item->getId();
        $this->assertEquals($expect, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_with_multiple_items()
    {
        // Define the expected data --------------------
        $data = array();

        $data[] = array('id' => 2, 'name' => 'Windsor');
        // ---------------------------------------------

        // Check if the actual results returned ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->where('u.name')->equals('Windsor');

        $result = new Result($this->pdo);

        $actual = $result->items($query);

        $this->assertEquals($data, $actual);
        // ----------------------------------------
    }

    /**
     * @return void
     */
    public function test_with_single_item()
    {
        // Define the expected data ------------------
        $data = array('id' => 2, 'name' => 'Windsor');
        // -------------------------------------------

        // Check if the actual results returned ---
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->where('u.name')->equals('Windsor');

        $result = new Result($this->pdo);

        $actual = $result->first($query);

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

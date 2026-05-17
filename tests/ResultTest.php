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
    public function test_passed_if_result_returns_entities()
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
    public function test_passed_if_result_returns_entity()
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
    public function test_passed_if_result_returns_item()
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
    public function test_passed_if_result_returns_items()
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
    public function test_passed_if_result_skips_unknown_columns()
    {
        // Define the expected data --------
        $user = new User;

        $user->setId(2)->setName('Windsor');
        // ---------------------------------

        // Check if the actual results returned ----
        $query = new User;

        $query->select('id, name, 1 AS extra_field')
            ->from('users')
            ->where('name')->equals('Windsor');

        $result = new UserResult($this->pdo);

        $item = $result->first($query);

        $expect = $user->getName();
        $actual = $item->getName();
        $this->assertEquals($expect, $actual);

        $expect = $user->getId();
        $actual = $item->getId();
        $this->assertEquals($expect, $actual);
        // -----------------------------------------
    }

    /**
     * @return void
     */
    public function test_passed_if_first_returns_empty_for_no_rows()
    {
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->where('u.name')->equals('NonExistent');

        $result = new Result($this->pdo);

        $actual = $result->first($query);

        $this->assertEmpty($actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_first_returns_empty_for_no_rows_entity()
    {
        $query = new User;

        $query->select('id, name')->from('users')
            ->where('name')->equals('NonExistent');

        $result = new UserResult($this->pdo);

        $actual = $result->first($query);

        $this->assertInstanceOf('Rougin\Ezekiel\Fixture\Entities\User', $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_items_returns_empty_for_no_rows()
    {
        $query = new Query;

        $query->select('u.*')->from('users u')
            ->where('u.name')->equals('NonExistent');

        $result = new Result($this->pdo);

        $actual = $result->items($query);

        $this->assertEquals(array(), $actual);
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        // Initialize the database --------
        $pdo = new \PDO('sqlite::memory:');

        $attr = \PDO::ATTR_ERRMODE;

        $key = \PDO::ERRMODE_EXCEPTION;

        $pdo->setAttribute($attr, $key);
        // --------------------------------

        // Create the table and its initial data -----------------------
        $query = 'CREATE TABLE users (id INTEGER, name TEXT)';

        $pdo->exec($query);

        $query = 'INSERT INTO users (id, name) VALUES (2, \'Windsor\')';

        $pdo->exec($query);
        // -------------------------------------------------------------

        $this->pdo = $pdo;
    }
}

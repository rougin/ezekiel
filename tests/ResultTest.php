<?php

namespace Rougin\Ezekiel;

use Rougin\Ezekiel\Fixture\Entities\User;

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

        $expected = array($user);
        // ---------------------------------

        // Check if the actual results returned -----------
        $query = new User;

        $query->select('id, name')->from('users')
            ->where('name')->equals('Windsor');

        $result = new Result($this->pdo);

        /** @var \Rougin\Ezekiel\Fixture\Entities\User[] */
        $actual = $result->get($query);

        $id = $actual[0]->getId();

        $name = $actual[0]->getName();

        $this->assertCount(count($expected), $actual);

        $this->assertEquals($user->getId(), $id);

        $this->assertEquals($user->getName(), $name);
        // ------------------------------------------------
    }

    /**
     * @return void
     */
    public function test_with_entity()
    {
        // Define the expected data ------------
        $expected = new User;

        $expected->setId(2)->setName('Windsor');
        // -------------------------------------

        // Check if the actual results returned ---------
        $query = new User;

        $query->select('id, name')->from('users')
            ->where('name')->equals('Windsor');

        $result = new Result($this->pdo);

        /** @var \Rougin\Ezekiel\Fixture\Entities\User */
        $actual = $result->first($query);

        $id = $actual->getId();

        $name = $actual->getName();

        $this->assertEquals($expected->getId(), $id);

        $this->assertEquals($expected->getName(), $name);
        // ----------------------------------------------
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

        $actual = $result->get($query);

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

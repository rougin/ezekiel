<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Active\Fixture\User;
use Rougin\Ezekiel\Testcase;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class BuilderTest extends Testcase
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @return void
     */
    public function test_passed_if_get_returns_models()
    {
        $this->seedUser('John');

        $results = $this->newQuery()->get();

        $this->assertCount(1, $results);

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\User', $results[0]);
    }

    /**
     * @return void
     */
    public function test_passed_if_first_returns_model()
    {
        $this->seedUser('Alice');

        $user = $this->newQuery()->first();

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\User', $user);
    }

    /**
     * @return void
     */
    public function test_passed_if_first_returns_null()
    {
        $user = $this->newQuery()->where('name', '=', 'None')->first();

        $this->assertNull($user);
    }

    /**
     * @return void
     */
    public function test_passed_if_find_returns_model()
    {
        $this->seedUser('Bob');

        $all = $this->newQuery()->get();

        $id = $all[0]->getAttribute('id');

        $user = $this->newQuery()->find($id);

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\User', $user);

        $this->assertEquals($id, $user->getAttribute('id'));
    }

    /**
     * @return void
     */
    public function test_passed_if_find_returns_null()
    {
        $user = $this->newQuery()->find(999);

        $this->assertNull($user);
    }

    /**
     * @return void
     */
    public function test_passed_if_find_or_fail_works()
    {
        $this->seedUser('Charlie');

        $all = $this->newQuery()->get();

        $id = $all[0]->getAttribute('id');

        $user = $this->newQuery()->findOrFail($id);

        $this->assertEquals($id, $user->getAttribute('id'));
    }

    /**
     * @return void
     */
    public function test_failed_if_find_or_fail_throws()
    {
        $this->doExpectException('RuntimeException');

        $this->newQuery()->findOrFail(9999);
    }

    /**
     * @return void
     */
    public function test_passed_if_count_returns_count()
    {
        $this->seedUser('A');

        $this->seedUser('B');

        $count = $this->newQuery()->count();

        $this->assertEquals(2, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_equals_works()
    {
        $this->seedUser('David');

        $users = $this->newQuery()->where('name', '=', 'David')->get();

        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_with_two_args()
    {
        $this->seedUser('Eve');

        $users = $this->newQuery()->where('name', 'Eve')->get();

        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_or_where_works()
    {
        $this->seedUser('Frank');

        $this->seedUser('Grace');

        $users = $this->newQuery()->where('name', '=', 'Frank')
            ->orWhere('name', '=', 'Grace')->get();

        $this->assertCount(2, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_in_works()
    {
        $this->seedUser('Henry');

        $this->seedUser('Ivy');

        $users = $this->newQuery()->whereIn('name', array('Henry', 'Ivy'))->get();

        $this->assertCount(2, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_null_works()
    {
        $this->pdo->exec('INSERT INTO users (name, age) VALUES (\'Jack\', NULL)');

        $users = $this->newQuery()->whereNull('age')->get();

        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_not_null_works()
    {
        $this->seedUser('Kate');

        $users = $this->newQuery()->whereNotNull('age')->get();

        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_order_by_asc_works()
    {
        $this->seedUser('B');

        $this->seedUser('A');

        $users = $this->newQuery()->orderBy('name')->get();

        $this->assertEquals('A', $users[0]->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_order_by_desc_works()
    {
        $this->seedUser('X');

        $this->seedUser('Y');

        $users = $this->newQuery()->orderBy('name', 'desc')->get();

        $this->assertEquals('Y', $users[0]->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_limit_works()
    {
        $this->seedUser('L1');

        $this->seedUser('L2');

        $this->seedUser('L3');

        $users = $this->newQuery()->limit(2)->get();

        $this->assertCount(2, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_offset_works()
    {
        $this->seedUser('L1');

        $this->seedUser('L2');

        $this->seedUser('L3');

        $users = $this->newQuery()->orderBy('name')->offset(1)->limit(2)->get();

        $this->assertCount(2, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_group_by_works()
    {
        $this->seedUser('Test');

        $users = $this->newQuery()->groupBy('name')->get();

        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_create_inserts_row()
    {
        $user = $this->newQuery()->create(array('name' => 'Created', 'age' => 20));

        $this->assertNotNull($user->getAttribute('id'));

        $this->assertEquals('Created', $user->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_update_modifies_rows()
    {
        $this->seedUser('Old');

        $count = $this->newQuery()->where('name', '=', 'Old')
            ->update(array('name' => 'New'));

        $this->assertEquals(1, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_delete_removes_rows()
    {
        $this->seedUser('Del');

        $count = $this->newQuery()->where('name', '=', 'Del')->delete();

        $this->assertEquals(1, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_select_overrides_cols()
    {
        $this->seedUser('Sel');

        $users = $this->newQuery()->select('name')->get();

        $first = $users[0];

        $this->assertNull($first->getAttribute('id'));

        $this->assertEquals('Sel', $first->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_select_defaults_all()
    {
        $this->seedUser('All');

        $users = $this->newQuery()->get();

        $first = $users[0];

        $this->assertNotNull($first->getAttribute('id'));

        $this->assertEquals('All', $first->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_distinct_works()
    {
        $this->seedUser('Dist');

        $this->seedUser('Dist');

        $users = $this->newQuery()->distinct()->select('name')->get();

        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_with_operator()
    {
        $this->seedUser('Minor', 15);

        $this->seedUser('Adult', 25);

        $users = $this->newQuery()->where('age', '>', 18)->get();

        $this->assertCount(1, $users);

        $this->assertEquals('Adult', $users[0]->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_where_like_works()
    {
        $this->seedUser('Smith');

        $users = $this->newQuery()->where('name', 'like', '%mit%')->get();

        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_where_not_equal()
    {
        $this->seedUser('One');

        $this->seedUser('Two');

        $users = $this->newQuery()->where('name', '!=', 'One')->get();

        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_model_returned()
    {
        $builder = new Builder(User::class, $this->pdo);

        $model = $builder->getModel();

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\User', $model);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_eagers_returns_array()
    {
        $builder = new Builder(User::class, $this->pdo);

        $builder->with('posts');

        $this->assertEquals(array('posts'), $builder->getEagers());
    }

    /**
     * @return \Rougin\Ezekiel\Active\Builder
     */
    protected function newQuery()
    {
        return new Builder(User::class, $this->pdo);
    }

    /**
     * @param string  $name
     * @param integer $age
     *
     * @return void
     */
    protected function seedUser($name, $age = 25)
    {
        $this->pdo->exec("INSERT INTO users (name, age) VALUES ('$name', $age)");
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $pdo = new \PDO('sqlite::memory:');

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, age INTEGER, created_at TEXT, updated_at TEXT)');

        $this->pdo = $pdo;
    }
}

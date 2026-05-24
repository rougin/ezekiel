<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Active\Fixture\CastUser;
use Rougin\Ezekiel\Active\Fixture\CustomTableUser;
use Rougin\Ezekiel\Active\Fixture\FloatUser;
use Rougin\Ezekiel\Active\Fixture\SoftDeleteModel;
use Rougin\Ezekiel\Active\Fixture\SoftDeleteUser;
use Rougin\Ezekiel\Active\Fixture\StringUser;
use Rougin\Ezekiel\Active\Fixture\User;
use Rougin\Ezekiel\Dialect\SqliteDialect;
use Rougin\Ezekiel\Schema\Design;
use Rougin\Ezekiel\Schema\Table;
use Rougin\Ezekiel\Testcase;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class ModelTest extends Testcase
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @return void
     */
    public function test_passed_if_all_returns_models()
    {
        $expect = 'Rougin\Ezekiel\Active\Fixture\User';

        $this->createUser('AllTest');

        $query = new User;

        $results = $query->all();

        $this->assertCount(1, $results);

        $this->assertInstanceOf($expect, $results[0]);
    }

    /**
     * @return void
     */
    public function test_passed_if_attribute_is_set()
    {
        $model = new User;

        $model->name = 'John';

        $this->assertTrue(isset($model->name));

        $this->assertFalse(isset($model->unknown));
    }

    /**
     * @return void
     */
    public function test_passed_if_cast_boolean()
    {
        $user = new User;

        /** @phpstan-ignore-next-line */
        $user->active = '1';

        $this->assertSame(true, $user->active);
    }

    /**
     * @return void
     */
    public function test_passed_if_cast_float()
    {
        $model = new FloatUser;

        $model->id = 1;

        /** @phpstan-ignore-next-line */
        $model->score = '95.5';

        $this->assertSame(95.5, $model->score);
    }

    /**
     * @return void
     */
    public function test_passed_if_cast_null_type()
    {
        $model = new SoftDeleteModel;

        $model->id = 1;

        $model->name = 'Test';

        $this->assertEquals('Test', $model->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_cast_returns_null_for_null()
    {
        $model = new FloatUser;

        $model->id = 1;

        $model->score = null;

        $this->assertNull($model->score);
    }

    /**
     * @return void
     */
    public function test_passed_if_cast_string()
    {
        $model = new StringUser;

        $model->id = 1;

        /** @phpstan-ignore-next-line */
        $model->notes = 123;

        $this->assertSame('123', $model->notes);
    }

    /**
     * @return void
     */
    public function test_passed_if_cast_unknown_type()
    {
        $model = new CastUser;

        $model->id = 1;

        $model->meta = '{"key":"val"}';

        $this->assertEquals('{"key":"val"}', $model->meta);
    }

    /**
     * @return void
     */
    public function test_passed_if_count_uses_scalar_bind()
    {
        $this->createUser('ScalarCount');

        $query = new User;

        $count = $query->where('name', 'ScalarCount')->count();

        $this->assertEquals(1, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_count_uses_where_in()
    {
        $one = $this->createUser('User1');
        $two = $this->createUser('User2');

        $ids = array($one->id, $two->id);

        $query = new User;

        $result = $query->whereIn('id', $ids)->count();

        $this->assertEquals(2, $result);
    }

    /**
     * @return void
     */
    public function test_passed_if_delete_on_nonexistent()
    {
        $model = new User;

        $model->name = 'Ghost';

        $triggered = false;

        set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$triggered)
        {
            $triggered = true;

            return true;
        });

        $model->delete();

        restore_error_handler();

        $this->assertTrue($triggered);
    }

    /**
     * @return void
     */
    public function test_passed_if_delete_works()
    {
        $user = $this->createUser('ToDelete');

        $user->delete();

        $query = new User;

        $found = $query->find($user->id);

        $this->assertNull($found);
    }

    /**
     * @return void
     */
    public function test_passed_if_filter_uses_default()
    {
        $this->createUser('DefaultOp');

        $query = new User;

        $found = $query->where('name', 'DefaultOp')->firstOrFail();

        $this->assertEquals('DefaultOp', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_filter_uses_greater()
    {
        $this->createUser('GTTest');

        $query = new User;

        $found = $query->where('id', 0)->orWhere('id', '>', 0)->firstOrFail();

        $this->assertEquals('GTTest', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_filter_uses_gte()
    {
        $user = $this->createUser('GTETest');

        $query = new User;

        $found = $query->where('id', 0)->orWhere('id', '>=', $user->id)->firstOrFail();

        $this->assertEquals('GTETest', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_filter_uses_less()
    {
        $this->createUser('LTTest');

        $query = new User;

        $found = $query->where('id', 0)->orWhere('id', '<', 99999)->firstOrFail();

        $this->assertEquals('LTTest', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_filter_uses_like()
    {
        $this->createUser('LikeTest');

        $query = new User;

        $found = $query->where('name', '')->orWhere('name', 'LIKE', '%Like%')->firstOrFail();

        $this->assertEquals('LikeTest', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_filter_uses_lte()
    {
        $user = $this->createUser('LTETest');

        $query = new User;

        $found = $query->where('id', 0)->orWhere('id', '<=', $user->id)->firstOrFail();

        $this->assertEquals('LTETest', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_filter_uses_not_equal()
    {
        $this->createUser('ShouldMatch');

        $query = new User;

        $found = $query->where('name', '')->orWhere('name', '!=', 'Other')->firstOrFail();

        $this->assertEquals('ShouldMatch', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_filter_uses_unknown_operator()
    {
        $this->createUser('Fallback');

        $query = new User;

        $found = $query->where('name', '')->orWhere('name', 'UNKNOWN', 'Fallback')->firstOrFail();

        $this->assertEquals('Fallback', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_find_or_fail_works()
    {
        $user = $this->createUser('FindOrFail');

        $query = new User;

        $found = $query->findOrFail($user->id);

        $this->assertEquals('FindOrFail', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_find_returns_model()
    {
        $user = $this->createUser('FindMe');

        $query = new User;

        $found = $query->find($user->id);

        $this->assertNotNull($found);

        $this->assertEquals('FindMe', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_find_returns_null()
    {
        $query = new User;

        $found = $query->find(99999);

        $this->assertNull($found);
    }

    /**
     * @return void
     */
    public function test_passed_if_first_empty_set()
    {
        $this->doExpectException('UnexpectedValueException');

        $query = new User;

        $query->firstOrFail();
    }

    /**
     * @return void
     */
    public function test_passed_if_first_returns_model()
    {
        $this->createUser('FirstModel');

        $query = new User;

        $found = $query->first();

        $this->assertNotNull($found);

        $this->assertEquals('FirstModel', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_first_returns_null()
    {
        $query = new User;

        $found = $query->where('name', 'None')->first();

        $this->assertNull($found);
    }

    /**
     * @return void
     */
    public function test_passed_if_foreign_key_derived()
    {
        $user = new User;

        $this->assertEquals('user_id', $user->getForeignKey());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_accessor_called()
    {
        $user = new User;

        $user->name = 'john';

        $this->assertEquals('john', $user->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_returns_models()
    {
        $this->createUser('GetTest');

        $query = new User;

        $results = $query->get();

        $this->assertCount(1, $results);

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\User', $results[0]);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_table_explicit()
    {
        $model = new CustomTableUser;

        $this->assertEquals('custom_table', $model->getTable());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_table_fallback()
    {
        $user = new User;

        $this->assertEquals('users', $user->getTable());
    }

    /**
     * @return void
     */
    public function test_passed_if_has_one_factory()
    {
        $user = $this->createUser('John');

        $relation = $user->profile();

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Relations\HasOne', $relation);
    }

    /**
     * @return void
     */
    public function test_passed_if_joining_table_sorted()
    {
        $user = new User;

        $expected = 'tags_users';

        $actual = $user->joiningTable('Rougin\Ezekiel\Active\Fixture\Tag');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_limit_uses_offset()
    {
        $this->createUser('First');

        $this->createUser('Second');

        $query = new User;

        $results = $query->limit(1)->offset(1)->get();

        $this->assertCount(1, $results);

        $this->assertEquals('Second', $results[0]->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_model_is_saved()
    {
        $model = new User;

        $model->name = 'John';

        $model->save();

        $this->assertTrue($model->id > 0);
    }

    /**
     * @return void
     */
    public function test_passed_if_model_is_updated()
    {
        $model = new User;

        $model->name = 'Old';

        $model->save();

        $id = $model->id;

        $model->name = 'New';

        $model->save();

        $this->assertEquals($id, $model->id);

        $this->assertEquals('New', $model->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_model_soft_delete()
    {
        $model = new SoftDeleteUser;

        $model->name = 'SoftDel';

        $model->save();

        $model->delete();

        /** @var \PDOStatement $stmt */
        $stmt = $this->pdo->query('SELECT deleted_at FROM users WHERE id = ' . $model->id);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertTrue(is_array($row));

        $this->assertNotNull($row['deleted_at']);
    }

    /**
     * @return void
     */
    public function test_passed_if_pdo_default_fallback()
    {
        $model = new User;

        $pdo = $model->getPdo();

        $this->assertInstanceOf('PDO', $pdo);
    }

    /**
     * @return void
     */
    public function test_passed_if_property_routes_to_method()
    {
        $model = new User;

        $actual = $model->exists;

        $this->assertFalse($actual);
    }

    /**
     * @return void
     */
    public function test_passed_if_save_creates_record()
    {
        $model = new User;

        $model->name = 'Direct Save';

        $model->save();

        $this->assertTrue($model->exists);

        $query = new User;

        $found = $query->findOrFail($model->id);

        $this->assertEquals('Direct Save', $found->name);
    }

    /**
     * @return void
     */
    public function test_passed_if_soft_delete_builder()
    {
        $model = new SoftDeleteModel;

        $model->limit(0);

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Model', $model);
    }

    /**
     * @return void
     */
    public function test_passed_if_table_is_derived()
    {
        $user = new User;

        $this->assertEquals('users', $user->getTable());
    }

    /**
     * @return void
     */
    public function test_passed_if_timestamps_are_set()
    {
        $model = new User;

        $model->name = 'Timestamped';

        $model->save();

        $this->assertNotNull($model->created_at);

        $this->assertNotNull($model->updated_at);
    }

    /**
     * @return void
     */
    public function test_passed_if_to_array_works()
    {
        $user = $this->createUser('ArrayTest');

        $data = $user->toArray();

        $this->assertEquals('ArrayTest', $data['name']);
    }

    /**
     * @return void
     */
    public function test_passed_if_update_works()
    {
        $user = $this->createUser('UpdOld');

        $user->update(array('name' => 'UpdNew'));

        $this->assertEquals('UpdNew', $user->name);
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Active\Fixture\User
     */
    protected function createUser($name)
    {
        $model = new User;

        $model->name = $name;

        $model->save();

        return $model;
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $pdo = new \PDO('sqlite::memory:');

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $dialect = new SqliteDialect;

        $table = new Table($dialect);

        $table->create('users', function (Design $d)
        {
            $d->increments('id');
            $d->text('name');
            $d->integer('age')->nullable();
            $d->text('active')->nullable();
            $d->timestamps();
            $d->softDeletes();
        });

        $pdo->exec($table->toSql());

        $table->create('posts', function (Design $d)
        {
            $d->increments('id');
            $d->integer('user_id');
            $d->text('title');
            $d->timestamps();
        });

        $pdo->exec($table->toSql());

        $table->create('tags', function (Design $d)
        {
            $d->increments('id');
            $d->text('name');
            $d->timestamps();
        });

        $pdo->exec($table->toSql());

        $table->create('post_tag', function (Design $d)
        {
            $d->integer('post_id');
            $d->integer('tag_id');
            $d->text('extra')->nullable();
            $d->timestamps();
        });

        $pdo->exec($table->toSql());

        $table->create('profiles', function (Design $d)
        {
            $d->increments('id');
            $d->integer('user_id');
            $d->text('bio');
            $d->timestamps();
        });

        $pdo->exec($table->toSql());

        Model::setPdo('default', $pdo);

        $this->pdo = $pdo;
    }
}

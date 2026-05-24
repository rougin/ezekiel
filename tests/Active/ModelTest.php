<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Active\Fixture\User;
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
    public function test_passed_if_table_name_is_set()
    {
        $expected = 'my_table';

        $model = $this->getMockModel();

        $model->setTable($expected);

        $this->assertEquals($expected, $model->getTable());
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
    public function test_passed_if_key_name_is_returned()
    {
        $model = $this->getMockModel();

        $this->assertEquals('id', $model->getKeyName());
    }

    /**
     * @return void
     */
    public function test_passed_if_key_value_is_returned()
    {
        $model = $this->getMockModelWithPdo();

        $model->fill(array('id' => 5, 'name' => 'Test'));

        $this->assertEquals(5, $model->getKey());
    }

    /**
     * @return void
     */
    public function test_passed_if_model_is_saved()
    {
        $model = $this->getMockModelWithPdo();

        $model->fill(array('name' => 'John', 'age' => 30));

        $model->save();

        $this->assertTrue($model->getAttribute('id') > 0);
    }

    /**
     * @return void
     */
    public function test_passed_if_model_is_updated()
    {
        $model = $this->getMockModelWithPdo();

        $model->fill(array('name' => 'John'));
        $model->save();

        $id = $model->getAttribute('id');

        $model->fill(array('name' => 'Jane'));
        $model->save();

        $this->assertEquals($id, $model->getAttribute('id'));
        $this->assertEquals('Jane', $model->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_update_by_id_works()
    {
        $model = $this->getMockModelWithPdo();

        $model->fill(array('name' => 'Old'));
        $model->save();

        $id = $model->getAttribute('id');

        $updated = $model->update($id, array('name' => 'New'));

        $this->assertEquals($id, $updated->getAttribute('id'));
        $this->assertEquals('New', $updated->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_model_is_deleted()
    {
        $model = $this->getMockModelWithPdo();

        $model->fill(array('name' => 'John'));
        $model->save();

        $result = $model->delete();

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function test_passed_if_soft_delete_works()
    {
        $model = $this->getSoftDeleteModel();

        $model->fill(array('name' => 'John'));
        $model->save();

        $model->delete();

        $this->assertTrue($model->trashed());

        $deleted = $model->getDeletedAtColumn();

        $this->assertNotNull($model->getAttribute($deleted));
    }

    /**
     * @return void
     */
    public function test_passed_if_restore_works()
    {
        $model = $this->getSoftDeleteModel();

        $model->fill(array('name' => 'John'));
        $model->save();

        $model->delete();

        $this->assertTrue($model->trashed());

        $model->restore();

        $this->assertFalse($model->trashed());
    }

    /**
     * @return void
     */
    public function test_passed_if_force_delete_works()
    {
        $model = $this->getSoftDeleteModel();

        $model->fill(array('name' => 'John'));
        $model->save();

        $result = $model->forceDelete();

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function test_passed_if_restore_no_soft_delete()
    {
        $model = $this->getMockModelWithPdo();

        $this->assertFalse($model->restore());
    }

    /**
     * @return void
     */
    public function test_passed_if_fill_respects_fillable()
    {
        $user = new User;

        $user->setPdo($this->pdo);

        $user->fill(array('name' => 'John', 'unknown' => 'secret'));

        $this->assertEquals('John', $user->getAttribute('name'));

        $this->assertNull($user->getAttribute('unknown'));
    }

    /**
     * @return void
     */
    public function test_failed_if_fill_rejects_guarded()
    {
        $model = $this->getGuardedModel();

        $model->fill(array('name' => 'John', 'password' => 'sec'));

        $this->assertEquals('John', $model->getAttribute('name'));

        $this->assertNull($model->getAttribute('password'));
    }

    /**
     * @return void
     */
    public function test_passed_if_force_fill_bypasses()
    {
        $model = $this->getGuardedModel();

        $model->forceFill(array('name' => 'John', 'password' => 'sec'));

        $this->assertEquals('John', $model->getAttribute('name'));

        $this->assertEquals('sec', $model->getAttribute('password'));
    }

    /**
     * @return void
     */
    public function test_passed_if_timestamps_are_set()
    {
        $model = new class () extends Model
        {
            protected $fillable = array('id', 'name', 'age');
        };

        $model->setTable('users');

        $model->setPdo($this->pdo);

        $model->fill(array('name' => 'John'));
        $model->save();

        $this->assertNotNull($model->getAttribute('created_at'));
        $this->assertNotNull($model->getAttribute('updated_at'));
    }

    /**
     * @return void
     */
    public function test_passed_if_casts_integers()
    {
        $user = new User;

        $user->setPdo($this->pdo);

        $user->fill(array('name' => 'John', 'age' => '30'));
        $user->save();

        $this->assertSame(30, $user->getAttribute('age'));
    }

    /**
     * @return void
     */
    public function test_passed_if_casts_booleans()
    {
        $user = new User;

        $user->setPdo($this->pdo);

        $user->fill(array('name' => 'John', 'active' => '1'));
        $user->save();

        $this->assertSame(true, $user->getAttribute('active'));
    }

    /**
     * @return void
     */
    public function test_passed_if_accessor_is_called()
    {
        $user = new User;

        $user->setRawAttributes(array('name' => 'john'), true);

        $this->assertEquals('john', $user->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_failed_if_delete_on_nonexistent()
    {
        $model = $this->getMockModelWithPdo();

        $this->assertNull($model->delete());
    }

    /**
     * @return void
     */
    public function test_passed_if_query_returns_builder()
    {
        $model = $this->getMockModelWithPdo();

        $builder = $model->newQuery();

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Builder', $builder);
    }

    /**
     * @return void
     */
    public function test_passed_if___get_uses_accessor()
    {
        $user = new User;

        $user->setRawAttributes(array('name' => 'john'), true);

        $this->assertEquals('john', $user->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if___set_works()
    {
        $user = new User;

        $user->setAttribute('name', 'Bob');

        $this->assertEquals('Bob', $user->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_relation_lazy_loaded()
    {
        $user = new User;

        $user->setPdo($this->pdo);

        $user->fill(array('name' => 'John'));
        $user->save();

        $posts = $user->getAttribute('posts');

        $this->assertTrue(is_array($posts));
    }

    /**
     * @return void
     */
    public function test_passed_if_qualify_column()
    {
        $user = new User;

        $expected = 'users.name';

        $this->assertEquals($expected, $user->qualifyColumn('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_qualify_column_dotted()
    {
        $user = new User;

        $this->assertEquals('a.b', $user->qualifyColumn('a.b'));
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
    public function test_passed_if_joining_table_sorted()
    {
        $user = new User;

        $expected = 'tags_users';

        $this->assertEquals($expected, $user->joiningTable('Rougin\Ezekiel\Active\Fixture\Tag'));
    }

    /**
     * @return void
     */
    public function test_passed_if_get_dirty_detects()
    {
        $model = $this->getMockModelWithPdo();

        $model->fill(array('name' => 'Old'));
        $model->save();

        $model->fill(array('name' => 'New'));

        $this->assertEquals('New', $model->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_sync_original_works()
    {
        $model = $this->getMockModelWithPdo();

        $model->fill(array('name' => 'John'));
        $model->save();

        $model->fill(array('name' => 'Jane'));

        $this->assertEquals('Jane', $model->getAttribute('name'));

        $model->save();

        $this->assertEquals('Jane', $model->getAttribute('name'));
    }

    /**
     * @return void
     */
    public function test_passed_if_set_raw_attributes()
    {
        $model = $this->getMockModelWithPdo();

        $model->setRawAttributes(array('name' => 'Raw', 'age' => 25), true);

        $this->assertEquals('Raw', $model->getAttribute('name'));
        $this->assertEquals(25, $model->getAttribute('age'));
    }

    /**
     * @return void
     */
    public function test_passed_if_connection_name_returned()
    {
        $model = $this->getMockModel();

        $this->assertEquals('default', $model->getConnectionName());
    }

    /**
     * @return void
     */
    public function test_passed_if_soft_delete_scopes_query()
    {
        $model = $this->getSoftDeleteModel();

        $builder = $model->newQuery();

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Builder', $builder);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_incrementing()
    {
        $model = $this->getMockModel();

        $this->assertTrue($model->getIncrementing());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_key_type()
    {
        $model = $this->getMockModel();

        $this->assertEquals('int', $model->getKeyType());
    }

    /**
     * @return void
     */
    public function test_passed_if_uses_timestamps()
    {
        $model = new User;

        $this->assertTrue($model->usesTimestamps());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_created_at_column()
    {
        $model = $this->getMockModel();

        $this->assertEquals('created_at', $model->getCreatedAtColumn());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_updated_at_column()
    {
        $model = $this->getMockModel();

        $this->assertEquals('updated_at', $model->getUpdatedAtColumn());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_deleted_at_column()
    {
        $model = $this->getMockModel();

        $this->assertEquals('deleted_at', $model->getDeletedAtColumn());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_qualified_key_name()
    {
        $user = new User;

        $this->assertEquals('users.id', $user->getQualifiedKeyName());
    }

    /**
     * @return void
     */
    public function test_passed_if_is_fillable()
    {
        $model = $this->getGuardedModel();

        $this->assertTrue($model->isFillable('name'));
        $this->assertFalse($model->isFillable('password'));
    }

    /**
     * @return void
     */
    public function test_passed_if_get_pdo()
    {
        $model = $this->getMockModelWithPdo();

        $this->assertSame($this->pdo, $model->getPdo());
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return \Rougin\Ezekiel\Active\Model
     */
    protected function getMockModel(array $attributes = array())
    {
        $model = new class ($attributes) extends Model
        {
            protected $timestamps = false;

            protected $fillable = array('id', 'name', 'age');
        };

        $model->setTable('users');

        return $model;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model
     */
    protected function getMockModelWithPdo()
    {
        $model = new class () extends Model
        {
            protected $timestamps = false;

            protected $fillable = array('id', 'name', 'age');
        };

        $model->setTable('users');

        $model->setPdo($this->pdo);

        return $model;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model
     */
    protected function getSoftDeleteModel()
    {
        $model = new class () extends Model
        {
            protected $softDelete = true;

            protected $timestamps = false;

            protected $fillable = array('id', 'name', 'deleted_at', 'created_at', 'updated_at');
        };

        $model->setTable('users');

        $model->setPdo($this->pdo);

        return $model;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model
     */
    protected function getGuardedModel()
    {
        $model = new class () extends Model
        {
            protected $timestamps = false;

            protected $fillable = array('name');

            protected $guarded = array('password');
        };

        $model->setTable('users');

        $model->setPdo($this->pdo);

        return $model;
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $pdo = new \PDO('sqlite::memory:');

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, age INTEGER, active TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT)');

        $pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, title TEXT, created_at TEXT, updated_at TEXT)');

        $this->pdo = $pdo;
    }
}

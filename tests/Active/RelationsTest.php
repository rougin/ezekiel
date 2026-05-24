<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Active\Fixture\Post;
use Rougin\Ezekiel\Active\Fixture\Profile;
use Rougin\Ezekiel\Active\Fixture\Tag;
use Rougin\Ezekiel\Active\Fixture\User;
use Rougin\Ezekiel\Testcase;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class RelationsTest extends Testcase
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @return void
     */
    public function test_passed_if_attach_pivot_works()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('PHP');

        $post = $this->createPost($user->id, 'Article');

        $relation = $post->tags();

        $relation->attach($tag->id, array('extra' => 'bonus'));

        /** @var \PDOStatement $stmt */
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM post_tag');

        $count = (int) $stmt->fetchColumn();

        $this->assertEquals(1, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_many()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('Tech');

        $post = $this->createPost($user->id, 'Post');

        $post->tags()->attach($tag->id);

        $tags = $post->tags;

        $this->assertTrue(is_array($tags));
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_many_attach_timestamps()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('TS');

        $post = $this->createPost($user->id, 'TS Post');

        $relation = $post->tags()->withTimestamps();

        $relation->attach($tag->id);

        /** @var \PDOStatement $stmt */
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM post_tag WHERE created_at IS NOT NULL');

        $count = (int) $stmt->fetchColumn();

        $this->assertEquals(1, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_many_cached()
    {
        $user = $this->createUser('CacheBM');

        $results = $user->tags();

        $first = $results->getAll();

        $second = $results->getAll();

        $this->assertSame($first, $second);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_many_default_pivot()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('DefaultPivot');

        $this->pdo->exec("INSERT INTO post_tag (post_id, tag_id) VALUES ($user->id, $tag->id)");

        $results = $user->tags();

        $models = $results->getAll();

        $this->assertCount(1, $models);

        $this->assertNotNull($models[0]->pivot);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_many_empty()
    {
        $expect = 'Rougin\Ezekiel\Active\Relations\BelongsToMany';

        $user = $this->createUser('EmptyBM');

        $this->assertInstanceOf($expect, $user->tags());
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_many_no_pivot_filter()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('NoFilter');

        $post = $this->createPost($user->id, 'NF Post');

        $post->tags()->attach($tag->id);

        $tags = $post->tags;

        $this->assertCount(1, $tags);

        $this->assertNotNull($tags[0]->pivot);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_many_zero_rows()
    {
        $expect = 'Rougin\Ezekiel\Active\Relations\BelongsToMany';

        $user = $this->createUser('Zero');

        $results = $user->tags();

        $results->getAll();

        $this->assertInstanceOf($expect, $results);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_null()
    {
        $this->createUser('John');

        $post = new Post;

        $post->user_id = 999;

        $post->title = 'No Parent';

        $found = $post->user;

        $this->assertNull($found);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_null_foreign()
    {
        $expect = 'Rougin\Ezekiel\Active\Relations\BelongsTo';

        $post = new Post;

        $result = $post->user();

        $this->assertInstanceOf($expect, $result);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_null_value()
    {
        $post = new Post;

        $user = $post->user;

        $this->assertNull($user);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_works()
    {
        $user = $this->createUser('John');

        $post = $this->createPost($user->id, 'Article');

        $expect = 'Rougin\Ezekiel\Active\Fixture\User';

        $found = $post->user;

        $this->assertInstanceOf($expect, $found);

        $this->assertEquals($user->id, $found->id);
    }

    /**
     * @return void
     */
    public function test_passed_if_builder_soft_deletes_filter()
    {
        $pdo = new \PDO('sqlite::memory:');

        Manager::set('test_soft', $pdo);

        $dialect = \Rougin\Ezekiel\Dialect::fromPdo($pdo);

        $builder = new \Rougin\Ezekiel\Active\Builder($dialect, 'test_table');

        $builder->useSoftDeletes(true);

        $builder->where('name', 'Test');

        $query = $builder->toQuery();

        $sql = $query->toSql();

        $this->doAssertContains('deleted_at', $sql);
    }

    /**
     * @return void
     */
    public function test_passed_if_eager_loads_relation()
    {
        $user = $this->createUser('Eager');

        $this->createPost($user->id, 'Post 1');

        $this->createPost($user->id, 'Post 2');

        $query = new User;

        $found = $query->with('posts')->findOrFail($user->id);

        $this->assertEquals('Eager', $found->name);

        $this->assertCount(2, $found->posts);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_all_is_cached()
    {
        $user = $this->createUser('Cache');

        $post = $this->createPost($user->id, 'Cached');

        $tag = $this->createTag('Cache');

        $post->tags()->attach($tag->id);

        $first = $post->tags;

        $second = $post->tags;

        $this->assertSame($first, $second);
    }

    /**
     * @return void
     */
    public function test_passed_if_has_many_null_local()
    {
        $user = new User;

        $posts = $user->posts;

        $this->assertCount(0, $posts);
    }

    /**
     * @return void
     */
    public function test_passed_if_has_many_returns_empty()
    {
        $user = $this->createUser('John');

        $posts = $user->posts;

        $this->assertTrue(is_array($posts));

        $this->assertCount(0, $posts);
    }

    /**
     * @return void
     */
    public function test_passed_if_has_many_returns_models()
    {
        $user = $this->createUser('John');

        $this->createPost($user->id, 'Post 1');

        $this->createPost($user->id, 'Post 2');

        $posts = $user->posts;

        $this->assertTrue(is_array($posts));

        $this->assertCount(2, $posts);
    }

    /**
     * @return void
     */
    public function test_passed_if_has_one_returns_model()
    {
        $user = $this->createUser('HasOne');

        $this->createProfile($user->id, 'My bio');

        $expect = 'Rougin\Ezekiel\Active\Fixture\Profile';

        $profile = $user->profile;

        $this->assertInstanceOf($expect, $profile);

        $this->assertEquals('My bio', $profile->bio);
    }

    /**
     * @return void
     */
    public function test_passed_if_has_one_returns_null()
    {
        $user = new User;

        $profile = $user->profile;

        $this->assertNull($profile);
    }

    /**
     * @return void
     */
    public function test_passed_if_pivot_data_loaded()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('Laravel');

        $post = $this->createPost($user->id, 'Art');

        $post->tags()->attach($tag->id, array('extra' => 'framework'));

        $tags = $post->tags;

        $this->assertTrue(is_array($tags));

        $this->assertCount(1, $tags);

        $expect = 'Rougin\Ezekiel\Active\Fixture\Tag';

        $first = $tags[0];

        $this->assertInstanceOf($expect, $first);

        $this->assertEquals($tag->id, $first->id);
    }

    /**
     * @return void
     */
    public function test_passed_if_profile_belongs_to_instance()
    {
        $expect = 'Rougin\Ezekiel\Active\Relations\BelongsTo';

        $profile = new Profile;

        $result = $profile->user();

        $this->assertInstanceOf($expect, $result);
    }

    /**
     * @return void
     */
    public function test_passed_if_profile_belongs_to_null()
    {
        $this->createUser('John');

        $profile = new Profile;

        $profile->user_id = 999;

        $profile->bio = 'No Parent';

        $found = $profile->user;

        $this->assertNull($found);
    }

    /**
     * @return void
     */
    public function test_passed_if_profile_belongs_to_null_new()
    {
        $profile = new Profile;

        $user = $profile->user;

        $this->assertNull($user);
    }

    /**
     * @return void
     */
    public function test_passed_if_profile_belongs_to_user()
    {
        $user = $this->createUser('John');

        $profile = $this->createProfile($user->id, 'My bio');

        $expect = 'Rougin\Ezekiel\Active\Fixture\User';

        $found = $profile->user;

        $this->assertInstanceOf($expect, $found);

        $this->assertEquals($user->id, $found->id);
    }

    /**
     * @return void
     */
    public function test_passed_if_tag_posts_empty()
    {
        $tag = $this->createTag('TagOnly');

        $posts = $tag->posts;

        $this->assertTrue(is_array($posts));

        $this->assertCount(0, $posts);
    }

    /**
     * @return void
     */
    public function test_passed_if_tag_posts_instance()
    {
        $expect = 'Rougin\Ezekiel\Active\Relations\BelongsToMany';

        $tag = $this->createTag('Instance');

        $this->assertInstanceOf($expect, $tag->posts());
    }

    /**
     * @return void
     */
    public function test_passed_if_tag_posts_works()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('Tech');

        $post = $this->createPost($user->id, 'Article');

        $post->tags()->attach($tag->id);

        $posts = $tag->posts;

        $this->assertTrue(is_array($posts));

        $this->assertCount(1, $posts);

        $expect = 'Rougin\Ezekiel\Active\Fixture\Post';

        $this->assertInstanceOf($expect, $posts[0]);

        $this->assertEquals($post->id, $posts[0]->id);
    }

    /**
     * @return void
     */
    public function test_passed_if_with_pivot_includes()
    {
        $expect = 'Rougin\Ezekiel\Active\Relations\BelongsToMany';

        $post = new Post;

        $relation = $post->tags()->withPivot('extra');

        $this->assertInstanceOf($expect, $relation);
    }

    /**
     * @return void
     */
    public function test_passed_if_with_timestamps_works()
    {
        $expect = 'Rougin\Ezekiel\Active\Relations\BelongsToMany';

        $post = new Post;

        $relation = $post->tags()->withTimestamps();

        $this->assertInstanceOf($expect, $relation);
    }

    /**
     * @param mixed  $userId
     * @param string $title
     *
     * @return \Rougin\Ezekiel\Active\Fixture\Post
     */
    protected function createPost($userId, $title)
    {
        $post = new Post;

        $post->user_id = $userId;

        $post->title = $title;

        $post->save();

        return $post;
    }

    /**
     * @param mixed  $userId
     * @param string $bio
     *
     * @return \Rougin\Ezekiel\Active\Fixture\Profile
     */
    protected function createProfile($userId, $bio)
    {
        $profile = new Profile;

        $profile->user_id = $userId;

        $profile->bio = $bio;

        $profile->save();

        return $profile;
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Active\Fixture\Tag
     */
    protected function createTag($name)
    {
        $tag = new Tag;

        $tag->name = $name;

        $tag->save();

        return $tag;
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Active\Fixture\User
     */
    protected function createUser($name)
    {
        $user = new User;

        $user->name = $name;

        $user->save();

        return $user;
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

        $pdo->exec('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, created_at TEXT, updated_at TEXT)');

        $pdo->exec('CREATE TABLE post_tag (post_id INTEGER, tag_id INTEGER, extra TEXT, created_at TEXT, updated_at TEXT)');

        $pdo->exec('CREATE TABLE profiles (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, bio TEXT, created_at TEXT, updated_at TEXT)');

        Model::setPdo('default', $pdo);

        $this->pdo = $pdo;
    }
}

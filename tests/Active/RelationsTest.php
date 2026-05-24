<?php

namespace Rougin\Ezekiel\Active;

use Rougin\Ezekiel\Active\Fixture\Post;
use Rougin\Ezekiel\Active\Fixture\Tag;
use Rougin\Ezekiel\Active\Fixture\User;
use Rougin\Ezekiel\Active\Relations\BelongsToMany;
use Rougin\Ezekiel\Active\Relations\HasMany;
use Rougin\Ezekiel\Active\Relations\HasOne;
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
    public function test_passed_if_has_many_returns_models()
    {
        $user = $this->createUser('John');

        $this->createPost($user->getKey(), 'Post 1');

        $this->createPost($user->getKey(), 'Post 2');

        $posts = $user->getAttribute('posts');

        $this->assertTrue(is_array($posts));

        $this->assertCount(2, $posts);
    }

    /**
     * @return void
     */
    public function test_passed_if_has_many_returns_empty()
    {
        $user = $this->createUser('John');

        $posts = $user->getAttribute('posts');

        $this->assertTrue(is_array($posts));

        $this->assertCount(0, $posts);
    }

    /**
     * @return void
     */
    public function test_passed_if_has_one_returns_model()
    {
        $user = $this->createUser('John');

        $user->setPdo($this->pdo);

        $postClass = 'Rougin\Ezekiel\Active\Fixture\Post';

        $relation = new HasOne($user, $postClass);

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Relations\HasOne', $relation);

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\User', $relation->getParent());

        $this->assertEquals('Rougin\Ezekiel\Active\Fixture\Post', $relation->getRelatedClass());

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Builder', $relation->getQuery());
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_works()
    {
        $user = $this->createUser('John');

        $post = $this->createPost($user->getKey(), 'Article');

        $p = $post->getAttribute('user');

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\User', $p);

        $this->assertEquals($user->getKey(), $p->getKey());
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_null()
    {
        $user = $this->createUser('John');

        $post = $this->createPost(999, 'No Parent');

        $p = $post->getAttribute('user');

        $this->assertNull($p);
    }

    /**
     * @return void
     */
    public function test_passed_if_belongs_to_many()
    {
        $tag = $this->createTag('Tech');

        $tags = $tag->getAttribute('posts');

        $this->assertTrue(is_array($tags));
    }

    /**
     * @return void
     */
    public function test_passed_if_attach_pivot_works()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('PHP');

        $post = $this->createPost($user->getKey(), 'Article');

        $relation = $this->createPostTagRelation($post);

        $relation->attach($tag->getKey(), array('extra' => 'bonus'));

        /** @var \PDOStatement $stmt */
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM post_tag');

        $count = (int) $stmt->fetchColumn();

        $this->assertEquals(1, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_detach_pivot_works()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('PHP');

        $post = $this->createPost($user->getKey(), 'Article');

        $relation = $this->createPostTagRelation($post);

        $relation->attach($tag->getKey());

        $relation->detach($tag->getKey());

        /** @var \PDOStatement $stmt */
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM post_tag');

        $count = (int) $stmt->fetchColumn();

        $this->assertEquals(0, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_detach_all_works()
    {
        $user = $this->createUser('John');

        $tag1 = $this->createTag('A');

        $tag2 = $this->createTag('B');

        $post = $this->createPost($user->getKey(), 'Article');

        $relation = $this->createPostTagRelation($post);

        $relation->attach($tag1->getKey());

        $relation->attach($tag2->getKey());

        $relation->detach();

        /** @var \PDOStatement $stmt */
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM post_tag');

        $count = (int) $stmt->fetchColumn();

        $this->assertEquals(0, $count);
    }

    /**
     * @return void
     */
    public function test_passed_if_pivot_data_loaded()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('Laravel');

        $post = $this->createPost($user->getKey(), 'Art');

        $relation = $this->createPostTagRelation($post);

        $relation->attach($tag->getKey(), array('extra' => 'framework'));

        $tags = $post->getAttribute('tags');

        $this->assertTrue(is_array($tags));

        $this->assertCount(1, $tags);

        $first = $tags[0];

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\Tag', $first);

        $this->assertEquals($tag->getKey(), $first->getKey());
    }

    /**
     * @return void
     */
    public function test_passed_if_pivot_foreign_key()
    {
        $user = $this->createUser('John');

        $tag = $this->createTag('PHP');

        $post = $this->createPost($user->getKey(), 'Article');

        $relation = $this->createPostTagRelation($post);

        $relation->attach($tag->getKey());

        $results = $relation->getResults();

        $this->assertNotEmpty($results);

        $first = $results[0];

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\Tag', $first);

        $this->assertEquals($tag->getKey(), $first->getKey());
    }

    /**
     * @return void
     */
    public function test_passed_if_with_pivot_includes()
    {
        $user = $this->createUser('John');

        $relation = $this->createPostTagRelation($user);

        $relation->withPivot('extra');

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Relations\BelongsToMany', $relation);
    }

    /**
     * @return void
     */
    public function test_passed_if_with_timestamps_works()
    {
        $user = $this->createUser('John');

        $relation = $this->createPostTagRelation($user);

        $relation->withTimestamps();

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Relations\BelongsToMany', $relation);
    }

    /**
     * @return void
     */
    public function test_passed_if_match_assigns_relations()
    {
        $user1 = $this->createUser('User1');

        $user2 = $this->createUser('User2');

        $p1 = $this->createPost($user1->getKey(), 'P1');

        $p2 = $this->createPost($user1->getKey(), 'P2');

        $p3 = $this->createPost($user2->getKey(), 'P3');

        $models = array($user1, $user2);

        $results = array($p1, $p2, $p3);

        $relation = $this->createHasManyRelation($user1);

        $relation->match($models, $results, 'posts');

        $u1posts = $user1->getAttribute('posts');

        $u2posts = $user2->getAttribute('posts');

        $this->assertTrue(is_array($u1posts));

        $this->assertTrue(is_array($u2posts));

        $this->assertCount(2, $u1posts);

        $this->assertCount(1, $u2posts);
    }

    /**
     * @return void
     */
    public function test_passed_if_init_relation_clears()
    {
        $user = $this->createUser('John');

        $relation = $this->createHasManyRelation($user);

        $models = $relation->newRelations(array($user), 'posts');

        $result = $models[0]->getAttribute('posts');

        $this->assertTrue(is_array($result));

        $this->assertCount(0, $result);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_parent_works()
    {
        $user = $this->createUser('John');

        $relation = $this->createHasManyRelation($user);

        $this->assertSame($user, $relation->getParent());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_related_works()
    {
        $user = $this->createUser('John');

        $relation = $this->createHasManyRelation($user);

        $related = $relation->getRelated();

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Fixture\Post', $related);
    }

    /**
     * @return void
     */
    public function test_passed_if_get_related_class()
    {
        $user = $this->createUser('John');

        $relation = $this->createHasManyRelation($user);

        $this->assertEquals('Rougin\Ezekiel\Active\Fixture\Post', $relation->getRelatedClass());
    }

    /**
     * @return void
     */
    public function test_passed_if_get_query_returns_builder()
    {
        $user = $this->createUser('John');

        $relation = $this->createHasManyRelation($user);

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Builder', $relation->getQuery());
    }

    /**
     * @return void
     */
    public function test_passed_if_add_eager_constraints()
    {
        $user = $this->createUser('John');

        $relation = $this->createHasManyRelation($user);

        $relation->addEagers(array($user));

        $this->assertInstanceOf('Rougin\Ezekiel\Active\Relations\HasMany', $relation);
    }

    /**
     * @return \Rougin\Ezekiel\Active\Relations\BelongsToMany
     */
    protected function createPostTagRelation(Model $post)
    {
        return new BelongsToMany(
            $post,
            Tag::class,
            'post_tag',
            'post_id',
            'tag_id',
            'id',
            'id'
        );
    }

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     *
     * @return \Rougin\Ezekiel\Active\Relations\HasMany
     */
    protected function createHasManyRelation(Model $parent)
    {
        return new HasMany($parent, 'Rougin\Ezekiel\Active\Fixture\Post', 'user_id', 'id');
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Active\Fixture\User
     */
    protected function createUser($name)
    {
        $user = new User;

        $user->setPdo($this->pdo);

        $user->fill(array('name' => $name));
        $user->save();

        return $user;
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

        $post->setPdo($this->pdo);

        $post->fill(array('user_id' => $userId, 'title' => $title));
        $post->save();

        return $post;
    }

    /**
     * @param string $name
     *
     * @return \Rougin\Ezekiel\Active\Fixture\Tag
     */
    protected function createTag($name)
    {
        $tag = new Tag;

        $tag->setPdo($this->pdo);

        $tag->fill(array('name' => $name));
        $tag->save();

        return $tag;
    }

    /**
     * @return void
     */
    protected function doSetUp()
    {
        $pdo = new \PDO('sqlite::memory:');

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, age INTEGER, created_at TEXT, updated_at TEXT, deleted_at TEXT)');

        $pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, title TEXT, created_at TEXT, updated_at TEXT)');

        $pdo->exec('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, created_at TEXT, updated_at TEXT)');

        $pdo->exec('CREATE TABLE post_tag (post_id INTEGER, tag_id INTEGER, extra TEXT)');

        $this->pdo = $pdo;
    }
}

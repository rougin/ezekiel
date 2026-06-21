# Ezekiel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-coverage]][link-coverage]
[![Total Downloads][ico-downloads]][link-downloads]

An expressive SQL query builder in PHP. This package is previously known as [Windstorm](https://github.com/rougin/ezekiel/tree/c95c77506087db19033997d1e752ce01c9294056).

## Why

I tried to unify [Doctrine](https://www.doctrine-project.org/) and [Eloquent](https://laravel.com/docs/5.0/eloquent) into a single interface for them to be swappable. Unfortunately the implementation is not possible because of the different core design patterns ([data mapper](https://en.wikipedia.org/wiki/Data_mapper_pattern) for Doctrine while [active record](https://en.wikipedia.org/wiki/Active_record_pattern) for Eloquent). I realized later that the one thing common for both is their query builder and it was also common on all existing ORM packages and SQL query builders.

## Installation

Install `Ezekiel` through [Composer](https://getcomposer.org/):

``` bash
$ composer require rougin/ezekiel
```

## Basic usage

Use the `Query` class to create SQL queries:

``` php
use Rougin\Ezekiel\Query;

$query = (new Query)
    ->select(array('u.id', 'u.name', 'p.name as position'))
    ->from('users u')
    ->leftJoin('positions p')->on('p.id', 'u.position_id')
    ->where('u.name')->like('%winds%')
    ->having('u.id')->greaterThan(0)
    ->orderBy('u.created_at')->desc();

// SELECT u.id, u.name, p.name as position
// FROM users u
// LEFT JOIN positions p ON p.id = u.position_id
// WHERE u.name LIKE ? HAVING u.id > ?
// ORDER BY u.created_at DESC
$sql = $query->toSql();

// array('name' => '%winds%', 'id' => 0)
$binds = $query->getBinds();
```

After creating the query, use the `Result` class to return its contents:

``` php
use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\Result;

$query = (new Query)
    ->select(array('u.id', 'u.name'))
    ->from('users', 'u')
    ->where('name')->like('%winds%')
    ->orderBy('created_at')->desc();

$pdo = /** returns a PDO instance */;

$result = new Result($pdo);

$items = $result->items($query);

echo json_encode($items);
```

``` json
[
  {
    "id": 2,
    "name": "Windsor",
    "created_at": "2018-10-15 23:09:47",
    "updated_at": null
  },
  {
    "id": 1,
    "name": "Windstorm",
    "created_at": "2018-10-15 23:06:28",
    "updated_at": null
  },
  {
    "id": 3,
    "name": "Windsy",
    "created_at": "2018-10-15 23:14:45",
    "updated_at": null
  }
]
```

For returning only one item from the result, use the `first` method instead:

``` php
// ...

use Rougin\Ezekiel\Result;

// ...

$result = new Result($pdo);

$items = $result->first($query);

echo json_encode($items);
```

``` json
{
  "id": 2,
  "name": "Windsor",
  "created_at": "2018-10-15 23:09:47",
  "updated_at": null
}
```

## Using entities

For mapping query results into an entity object, the entity can be extended to the `Entity` class:

```php
// src/Entities/User.php

namespace Test\Entities;

use Rougin\Ezekiel\Entity;

class User extends Entity
{
    protected $id;

    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
```

If an `Entity` is passed to the `Result` class, the results will be automatically to new instances of that `Entity`:

```php
use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\Result;
use Test\Entities\User;

$user = (new User)
    ->select('id, name')->from('users')
    ->where('name')->equals('Windsor');

$pdo = /** returns a PDO instance */ ;

$result = new Result($pdo);

/** @var \Rougin\Ezekiel\Fixture\Entities\User[] */
$users = $result->items($user);

foreach ($users as $user)
{
    echo 'Hello ' . $user->getName() . '!<br>';
}
```

## Using "Active"

The `Active` namespace provides a lightweight active-record implementation on top of Ezekiel's query builder. It serves as a drop-in replacement for [Eloquent](https://laravel.com/docs/eloquent) models without external dependencies:

```php
namespace App\Models;

use Rougin\Ezekiel\Active\Model;

class User extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = array('age' => 'integer');

    /**
     * @var string[]
     */
    protected $fillable = array('name', 'age');

    /**
     * @param string $value
     *
     * @return string
     */
    public function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    /**
     * @return \Rougin\Ezekiel\Active\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(__NAMESPACE__ . '\Post');
    }
}
```

To setup a model instance, use the existing `PDO` instance and set it to the same model (e.g., `User`):

```php
$pdo = new \PDO('sqlite::memory:');

$user = new User;

$user->setPdo($pdo);
```

Then use the available Eloquent methods for doing CRUD operations:

```php
// ...

// Create a new user -----------
$data = array('name' => 'John');

$data['age'] = 25;

$item = $user->create($data);
// -----------------------------

// Find or show all users ------------
$items = $user->get();

$item = $user->find(1);

$adults = $user->where('age', '>', 18)
    ->get();
// -----------------------------------

// Update the current user -----
$data = array('age' => 26);

$user->update($user->id, $data);
// -----------------------------

// Delete the specified user ---------
$model = $user->where('name', 'John');

$model = $model->first()->delete();
// -----------------------------------
```

### Query builder methods

The `Builder` class wraps Ezekiel's `Query` and provides an Eloquent-compatible fluent interface:

| Method | Description |
|---|---|
| `where($column, $value)` | Basic where clause (`$operator` optional as second argument) |
| `orWhere($column, $value)` | OR where clause (`$operator` optional as second argument) |
| `whereIn($column, $values)` | WHERE IN clause |
| `whereLike($column, $value)` | WHERE LIKE clause |
| `orWhereLike($column, $value)` | OR WHERE LIKE clause |
| `whereNull($column)` | WHERE IS NULL |
| `whereNotNull($column)` | WHERE IS NOT NULL |
| `orderBy($column, $direction)` | ORDER BY clause |
| `limit($value)` | LIMIT clause |
| `offset($value)` | OFFSET clause |
| `groupBy($columns)` | GROUP BY clause |
| `with($relations)` | Eager load relationships |
| `select($columns)` | Override selected columns |
| `distinct()` | SELECT DISTINCT |
| `get($columns)` | Execute and return models |
| `first($columns)` | Return first model or null |
| `find($id, $columns)` | Find by primary key |
| `findOrFail($id, $columns)` | Find or throw RuntimeException |
| `count()` | Return row count |
| `create($attrs)` | Insert and return model |
| `update($values)` | Bulk update matching rows |
| `delete()` | Delete matching rows |

### Accessors and mutators

Accessors follow Eloquent's `getFooAttribute($value)` convention. The column name in `snake_case` is transformed to `StudlyCase` internally:

```php
class User extends Model
{
    public function getFullNameAttribute($value)
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }
}

echo $user->full_name; // triggers getFullNameAttribute

$user->email = 'JOHN@EXAMPLE.COM'; // triggers setEmailAttribute
```

### Attribute casting

The `$casts` property automatically converts attributes to their PHP types:

```php
protected $casts = array(

    'age' => 'integer',
    'active' => 'boolean',
    'price' => 'float',
    'notes' => 'string',

);
```

### Mass assignment protection

Use `$fillable` to whitelist or `$guarded` to blacklist attributes:

```php
class User extends Model
{
    /**
     * Only these keys can be mass-assigned.
     *
     * @var string[]
     */
    protected $fillable = array('name', 'email');

    /**
     * These keys are blocked (default: '*' blocks all).
     *
     * @var string[]
     */
    protected $guarded = array('password', 'is_admin');
}

// Bypass protection
$user->forceFill(array('is_admin' => true));
```

### Timestamps

By default, `created_at` and `updated_at` are managed automatically. Use `$timestamps = false` to disable it in a specified `Model`:

```php
protected $timestamps = false;
```

### Soft deletes

Activate soft deleting by setting `$softDelete = true`. Instead of removing rows, the `deleted_at` column is set:

```php
class User extends Model
{
    /**
     * @var boolean
     */
    protected $softDelete = true;
}

// Sets "deleted_at", row remains
$user->delete();

// Returns "true" if soft-deleted 
$user->trashed();

// Sets "deleted_at" as "null"
$user->restore();

// Permanently removes the row
$user->forceDelete();
```

### Relationships

`Active` supports `hasMany`, `hasOne`, `belongsTo`, and `belongsToMany`:

```php
// One-to-many
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

// Inverse
class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// Many-to-many
class Post extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id')
            ->withPivot('extra');
    }
}
```

Lazy loading and eager loading are both supported:

```php
// Lazy
$user = $user->find(1);

foreach ($user->posts as $post)
{
    // ...
}

// Eager
$users = $user->with('posts')->get();
```

Pivot data is stored as `\stdClass` on the loaded model:

```php
$tags = $post->tags;

echo $tags[0]->pivot->extra;
```

While the `attach` and `detach` methods manage pivot table rows:

```php
$post->tags()->attach($tagId, array('extra' => 'value'));

$post->tags()->detach($tagId);
```

### Table name derivation

When `$table` is not set, the table name is derived from the class name by converting `PascalCase` to `snake_case` and pluralising:

```php
class UserProfile extends Model
{
    // If not specified, it will be "user_profiles"
}

class User extends Model
{
    /**
     * If not specified, it will be "users".
     *
     * @var string
     */
    protected $table = 'app_users';
}          
```

## Available methods

All available SQL statements should be supported by `Ezekiel`. These includes `DELETE FROM`, `INSERT INTO`, `SELECT`, and `UPDATE`:

### DELETE

``` php
use Rougin\Ezekiel\Query;

$query = (new Query)
    ->deleteFrom('users')
    ->where('id')->equals(12);

// DELETE FROM users WHERE id = ?
$sql = $query->toSql();

// array('id' => 12)
$binds = $query->getBinds();
```

### INSERT

``` php
use Rougin\Ezekiel\Query;

$query = (new Query)
    ->insertInto('users')
    ->values(array('name' => 'Ezekiel', 'age' => 20));

// INSERT INTO users (name, age) VALUES (?, ?)
$sql = $query->toSql();

// array('name' => 'Ezekiel', 'age' => 20)
$binds = $query->getBinds();
```

For batch inserting multiple rows, pass an array of associative arrays to `VALUES`:

``` php
$query = (new Query)
    ->insertInto('users')
    ->values(array(
        array('name' => 'Alice', 'age' => 25),
        array('name' => 'Bob', 'age' => 30),
    ));

// INSERT INTO users (name, age) VALUES (?, ?), (?, ?)
$sql = $query->toSql();

// array('Alice', 25, 'Bob', 30)
$binds = $query->getBinds();
```

### SELECT

``` php
$query = (new Query)
    ->select(array('u.id', 'u.name'))
    ->from('users u')
    ->where('u.name')->like('%winds%')
    ->orderBy('u.created_at')->desc();

// SELECT u.id, u.name FROM users u
// WHERE u.name LIKE ?
// ORDER BY u.created_at DESC
$sql = $query->toSql();

// array('name' => '%winds%')
$binds = $query->getBinds();
```

To select distinct values, call `DISTINCT` on the select builder before `FROM`:

``` php
$query = (new Query)
    ->select('name')->distinct()
    ->from('users');

// SELECT DISTINCT name FROM users
$sql = $query->toSql();
```

For granular conditions, use the `whereGroup` method to enclose multiple conditions in parentheses:

``` php
$query = (new Query)
    ->select('*')->from('users')
    ->where('status')->equals(1)
    ->whereGroup(function (Query $query)
    {
        $query->where('name')->equals('Alice')
            ->orWhere('name')->equals('Bob');
    });

// SELECT * FROM users
// WHERE status = ? AND (name = ? OR name = ?)
$sql = $query->toSql();

// array('status' => 1, 'name' => array('Alice', 'Bob'))
$binds = $query->getBinds();
```

In addition to the standard comparison operators, `BETWEEN` and `NOTBETWEEN` are also supported:

``` php
$query = (new Query)
    ->select('*')->from('users')
    ->where('age')->between(6, 7);

// SELECT * FROM users WHERE age BETWEEN ? AND ?
$sql = $query->toSql();

// array('age' => array(6, 7))
$binds = $query->getBinds();
```

### UPDATE

``` php
use Rougin\Ezekiel\Query;

$query = (new Query)
    ->update('users')
    ->set('name', 'Ezekiel')
    ->where('id')->equals(12);

// UPDATE users SET name = ? WHERE id = ?
$sql = $query->toSql();

// array('name' => 'Ezekiel', 'id' => 12)
$binds = $query->getBinds();
```

## Subqueries

`Ezekiel` supports subqueries in `WHERE` clauses and as derived tables in the `FROM` clause.

### WHERE IN subquery

``` php
$sub = (new Query)
    ->select('user_id')->from('posts')
    ->where('status')->equals(1);

$query = (new Query)
    ->select('*')->from('users')
    ->where('id')->in($sub);

// SELECT * FROM users
// WHERE id IN (SELECT user_id FROM posts WHERE status = ?)
$sql = $query->toSql();
```

### WHERE scalar comparison

``` php
$sub = (new Query)
    ->select('MAX(age)')->from('users');

$query = (new Query)
    ->select('*')->from('users')
    ->where('age')->equals($sub);

// SELECT * FROM users
// WHERE age = (SELECT MAX(age) FROM users)
$sql = $query->toSql();
```

### Derived table (FROM subquery)

``` php
$sub = (new Query)
    ->select('*')->from('users')
    ->where('active')->equals(1);

$query = (new Query)
    ->select('*')->from($sub, 'active_users');

// SELECT * FROM (SELECT * FROM users WHERE active = ?) active_users
$sql = $query->toSql();
```

## Schema builder

The `Table` class provides a fluent interface for building DDL statements such as `CREATE TABLE` and `DROP TABLE`:

``` php
use Rougin\Ezekiel\Schema\Table;
use Rougin\Ezekiel\Schema\Design;

$table = new Table;

$table->create('users', function (Design $d)
{
    $d->increments('id');
    $d->string('name', 100);
    $d->string('email')->unique();
    $d->integer('age')->defaultValue(0);
    $d->text('bio')->nullable();
    $d->timestamps();
});

// CREATE TABLE `users` (
//   `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
//   `name` VARCHAR(100) NOT NULL,
//   `email` VARCHAR(255) NOT NULL UNIQUE,
//   `age` INT NOT NULL DEFAULT 0,
//   `bio` TEXT,
//   `created_at` TIMESTAMP,
//   `updated_at` TIMESTAMP
// )
$sql = $table->toSql();
```

### Column types

| Type | SQL | Notes |
|---|---|---|
| `string(name, length)` | `VARCHAR(n)` | Default length: 255 |
| `integer(name, length)` | `INT(n)` | |
| `bigInteger(name)` | `BIGINT` | |
| `tinyInteger(name)` | `TINYINT` | |
| `boolean(name)` | `TINYINT(1)` | |
| `float(name)` | `FLOAT` | |
| `decimal(name, prec, scale)` | `DECIMAL(p, s)` | Default: `(8, 2)` |
| `text(name)` | `TEXT` | |
| `date(name)` | `DATE` | |
| `dateTime(name)` | `DATETIME` | |
| `timestamp(name)` | `TIMESTAMP` | |
| `increments(name)` | `INT AUTO_INCREMENT` | Also sets `NOT NULL PRIMARY KEY` |

> [!NOTE]
> SQL shown is for MySQL (the default dialect). Types are translated per dialect: e.g., `boolean()` becomes `BOOLEAN` in PostgreSQL, `BIT` in MSSQL, `INTEGER` in SQLite; `increments()` becomes `SERIAL` in PostgreSQL, `IDENTITY(1,1)` in MSSQL, `INTEGER AUTOINCREMENT` in SQLite.

### Column modifiers

| Modifier | SQL |
|---|---|
| `nullable()` | Omits `NOT NULL` |
| `defaultValue(value)` | `DEFAULT value` |
| `unique()` | `UNIQUE` |
| `primary()` | `PRIMARY KEY` |
| `autoIncrement()` | `AUTO_INCREMENT` |

### Table-level constraints

| Method | SQL |
|---|---|
| `primary('col')` | `PRIMARY KEY (`col`)` |
| `primary(array('a', 'b'))` | `PRIMARY KEY (`a`, `b`)` |
| `unique('col')` | `UNIQUE (`col`)` |
| `unique(array('a', 'b'))` | `UNIQUE (`a`, `b`)` |
| `index('col')` | `INDEX (`col`)` |
| `index(array('a', 'b'))` | `INDEX (`a`, `b`)` |

### Convenience methods

| Method | Adds |
|---|---|
| `timestamps()` | `created_at` and `updated_at` (`TIMESTAMP`, nullable) |
| `softDeletes()` | `deleted_at` (`TIMESTAMP`, nullable) |

### Drop operations

``` php
$table = new Table;
$table->drop('users');
// DROP TABLE `users`

$table->dropIfExists('users');
// DROP TABLE IF EXISTS `users`
```

## Snake case

All methods can be called in either `camelCase` or `snake_case`:

``` php
$query = (new Query)
    ->select(array('u.id', 'u.name'))
    ->from('users u')
    ->where('u.name')->like('%winds%')
    ->order_by('u.created_at')->desc();

$sql = $query->to_sql();

$binds = $query->get_binds();
```

## Dialects

`Ezekiel` generates SQL that adapts to the database platform with MySQL (`MysqlDialect`) as its default dialect. To target a different database, use the `setDialect` method:

``` php
use Rougin\Ezekiel\Dialect\PgsqlDialect;

$query = (new Query)->setDialect(new PgsqlDialect)
    ->select('*')->from('users')->limit(10);

// SELECT * FROM "users" LIMIT 10 OFFSET 0
$sql = $query->toSql();
```

When using `Result`, the dialect is automatically detected from the `PDO` connection:

``` php
$pdo = new \PDO('pgsql:host=localhost;dbname=test');

$result = new Result($pdo);

// Dialect is automatically set to PgsqlDialect on the Query
$items = $result->items($query);
```

To create a custom dialect, implement the class in `DialectInterface` or by extending it to `AbstractDialect`:

``` php
use Rougin\Ezekiel\Dialect\AbstractDialect;

class OracleDialect extends AbstractDialect
{
    public function getName()
    {
        return 'oracle';
    }

    public function getOpenQuoteChar()
    {
        return '"';
    }

    public function toLimit($limit, $offset)
    {
        return '';
    }
}
```

Custom dialects can also override `toColumn`, `toAlterTable`, `toCreateTable`, `toDropTable`, and `toDropTableIfExists` for generating platform-specific DDL statements (e.g., type translation, `ADD` vs `ADD COLUMN`, `AUTO_INCREMENT` vs `IDENTITY(1,1)`).

> [!NOTE]
> Available built-in dialects for `Ezekiel` include `MysqlDialect`, `PgsqlDialect`, `SqliteDialect`, and `MssqlDialect`.

## Renaming from `Windstorm`

As being renamed from `Windstorm`, this will introduce [backward compatibility](https://en.wikipedia.org/wiki/Backward_compatibility) (BC) breaks through out the source code. This was done to increase extensibility, simplicity and maintainbility and was discussed in one of [my blog post](https://roug.in/hello-world-again/) which aims to solve overengineering of my own open source packages:

> I also want to extend this plan to my personal packages as well like [Staticka](https://github.com/staticka/staticka) and [Transcribe](https://github.com/rougin/transcribe). With this, I will introduce backward compatibility breaks to them initially as it is hard to migrate their codebase due to minimal to no documentation being provided in its basic usage and its internals. As I checked their code, I realized that they are also over engineered, which is a mistake that I needed to atone for when updating my packages in the future.

Please see the [UPGRADING][link-upgrading] page for the specified breaking changes.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more recent changes.

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md) on how to contribute.

## License

The MIT License (MIT). Please see [LICENSE][link-license] for more information.

[ico-build]: https://img.shields.io/github/actions/workflow/status/rougin/ezekiel/build.yml?style=flat-square
[ico-coverage]: https://img.shields.io/codecov/c/github/rougin/ezekiel?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rougin/ezekiel.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/rougin/ezekiel.svg?style=flat-square

[link-build]: https://github.com/rougin/ezekiel/actions
[link-changelog]: https://github.com/rougin/ezekiel/blob/master/CHANGELOG.md
[link-contributors]: https://github.com/rougin/ezekiel/contributors
[link-coverage]: https://app.codecov.io/gh/rougin/ezekiel
[link-downloads]: https://packagist.org/packages/rougin/ezekiel
[link-license]: https://github.com/rougin/ezekiel/blob/master/LICENSE.md
[link-packagist]: https://packagist.org/packages/rougin/ezekiel
[link-upgrading]: https://github.com/rougin/ezekiel/blob/master/UPGRADING.md

# Ezekiel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-coverage]][link-coverage]
[![Total Downloads][ico-downloads]][link-downloads]

An expressive SQL query builder in PHP. This package is previously known as [Windstorm](https://github.com/rougin/ezekiel/tree/c95c77506087db19033997d1e752ce01c9294056).

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

$items = $result->get($query);

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
$users = $result->get($user);

foreach ($users as $user)
{
    echo 'Hello ' . $user->getName() . '!<br>';
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

## Renaming from `Windstorm`

As being renamed from `Windstorm`, this will introduce [backward compatibility](https://en.wikipedia.org/wiki/Backward_compatibility) (BC) breaks through out the source code. This was done to increase extensibility, simplicity and maintainbility and was discussed in one of [my blog post](https://roug.in/hello-world-again/) which aims to solve overengineering of my own open source packages:

> I also want to extend this plan to my personal packages as well like [Staticka](https://github.com/staticka/staticka) and [Transcribe](https://github.com/rougin/transcribe). With this, I will introduce backward compatibility breaks to them initially as it is hard to migrate their codebase due to minimal to no documentation being provided in its basic usage and its internals. As I checked their code, I realized that they are also over engineered, which is a mistake that I needed to atone for when updating my packages in the future.

Since the previous name was never released with a version, no `UPGRADING.md` was created. As such, please see [commit c95c775](https://github.com/rougin/ezekiel/tree/c95c77506087db19033997d1e752ce01c9294056) of this repository for the files that were removed or updated in this last commit.

## Changelog

Please see [CHANGELOG][link-changelog] for more information what has changed recently.

## Development

Includes tools for code quality, coding style, and unit tests.

### Code quality

Analyze code quality using [phpstan](https://phpstan.org/):

``` bash
$ phpstan
```

### Coding style

Enforce coding style using [php-cs-fixer](https://cs.symfony.com/):

``` bash
$ php-cs-fixer fix --config=phpstyle.php
```

### Unit tests

Execute unit tests using [phpunit](https://phpunit.de/index.html):

``` bash
$ vendor/bin/phpunit
```

## Credits

- [All contributors][link-contributors]

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

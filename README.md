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
    ->select(array('u.id', 'u.name'))
    ->from('users', 'u')
    ->where('name')->like('%winds%')
    ->orderBy('created_at')->desc();

// SELECT u.id, u.name FROM users u ---
// WHERE u.name LIKE :u_name ----------
// ORDER BY u.created_at DESC ---------
$sql = $query->toSql();
// ------------------------------------

// array(':u_name' => '%winds%') ---
$bindings = $query->bindings();
// ---------------------------------
```

After creating the query, use the `Result` class to return its results:

``` php
use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\Result;

// Sample query from previous example ---
$query = (new Query)
    ->select(array('u.id', 'u.name'))
    ->from('users', 'u')
    ->where('name')->like('%winds%')
    ->orderBy('created_at')->desc();
// --------------------------------------

$result = new Result($query);

echo json_encode($result->asItems());
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

## Renaming from `Windstorm`

As being renamed from `Windstorm`, this will introduce [backward compatibility](https://en.wikipedia.org/wiki/Backward_compatibility) (BC) breaks through out the source code. This was done to increase extensibility, simplicity and maintainbility. This was discussed in one of [my blog post](https://roug.in/hello-world-again/) which aims to solve overengineering my open source packages:

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
$ composer test
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

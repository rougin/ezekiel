As being renamed from `Windstorm`, this will introduce [backward compatibility](https://en.wikipedia.org/wiki/Backward_compatibility) (BC) breaks through out the source code. This was done to improve its extensibility, simplicity and maintainbility:

## Change `sql` to `toSql`

**Before**

```php
use Rougin\Windstorm\Query;

$query = /** instanceof Query */;

$sql = $query->sql();
```

**After**

```php
use Rougin\Ezekiel\Query;

$query = /** instanceof Query */;

$sql = $query->toSql();
```

## Change `binds` to `getBinds`

**Before**

```php
use Rougin\Windstorm\Query;

$query = /** instanceof Query */;

$binds = $query->binds();
```

**After**

```php
use Rougin\Ezekiel\Query;

$query = /** instanceof Query */;

$binds = $query->getBinds();
```

## Change `Result` constructor

The `Result` class constructor now requires a `PDO` instance:

**Before**

```php
use Doctrine\DBAL\Connection;
use Rougin\Windstorm\Doctrine\Database;
use Rougin\Windstorm\Result;

$conn = /** instanceof Connection */;

$db = new Database($conn);

$result = new Result($db);
```

**After**

```php
use Rougin\Ezekiel\Result;

$pdo = new PDO('sqlite::memory:');

$result = new Result($pdo);
```

## Change `Result::execute`

The `Query` class must now be passed to `items` or `first` methods:

**Before**

```php
use Doctrine\DBAL\Connection;
use Rougin\Windstorm\Doctrine\Result;
use Rougin\Windstorm\QueryInterface;

$conn = /** instanceof Connection */;
$query = /** instanceof QueryInterface */;

$result = new Result($conn);

$query = $query->select(array('u.*'));

$query = $query->from('users');

$result = $result->execute($query);

var_dump($result->items());
```

**After**

```php
use Rougin\Ezekiel\Query;
use Rougin\Ezekiel\Result;

$query = /** instanceof Query */;

$pdo = /** instanceof PDO */;

$result = new Result($pdo);

var_dump($result->items($query));
```

## Removed features

The following classes and interfaces have been removed for simplicity:

- `Rougin\Windstorm\MappingInterface`
- `Rougin\Windstorm\QueryRepository` and its mutators
- `Rougin\Windstorm\Relation`-related classes

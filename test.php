<?php

use Rougin\Ezekiel\Query;

require 'vendor/autoload.php';

$query = new Query;

$query->select('*')
    ->from('users')
    ->where('name')->equals('Royce')
    ->andWhere('age')->in([25, 30]);

echo $query->toSql();

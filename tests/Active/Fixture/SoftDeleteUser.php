<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SoftDeleteUser extends Model
{
    protected $softDeletes = true;

    protected $fillable = array('id', 'name');

    protected $table = 'users';
}

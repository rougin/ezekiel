<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @property integer     $id
 * @property string      $name
 * @property string|null $deleted_at
 *
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SoftDeleteUser extends Model
{
    /**
     * @var boolean
     */
    protected $softDeletes = true;

    /**
     * @var array<integer, string>
     */
    protected $fillable = array('id', 'name');

    /**
     * @var string
     */
    protected $table = 'users';
}

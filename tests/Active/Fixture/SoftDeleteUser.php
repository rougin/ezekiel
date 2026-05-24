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
    /**
     * @var boolean
     */
    protected $softDeletes = true;

    /**
     * @var string[]
     */
    protected $fillable = array('id', 'name');

    /**
     * @var string
     */
    protected $table = 'users';
}

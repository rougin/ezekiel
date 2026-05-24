<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class StringUser extends Model
{
    protected $casts = array('notes' => 'string');

    protected $fillable = array('id', 'notes');
}

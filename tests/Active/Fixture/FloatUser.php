<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class FloatUser extends Model
{
    protected $casts = array('score' => 'float');

    protected $fillable = array('id', 'score');
}

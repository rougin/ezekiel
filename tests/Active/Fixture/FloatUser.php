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
    /**
     * @var array<string, string>
     */
    protected $casts = array('score' => 'float');

    /**
     * @var string[]
     */
    protected $fillable = array('id', 'score');
}

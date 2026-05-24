<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @property integer    $id
 * @property float|null $score
 *
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
     * @var array<integer, string>
     */
    protected $fillable = array('id', 'score');
}

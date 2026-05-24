<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @property integer $id
 * @property string  $notes
 *
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class StringUser extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = array('notes' => 'string');

    /**
     * @var array<integer, string>
     */
    protected $fillable = array('id', 'notes');
}

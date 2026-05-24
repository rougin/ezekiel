<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CastUser extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = array('meta' => 'json');
}

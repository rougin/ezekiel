<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @property integer $id
 * @property string  $name
 *
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SoftDeleteModel extends Model
{
    /**
     * @var boolean
     */
    protected $softDeletes = true;
}

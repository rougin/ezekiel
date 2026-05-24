<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class User extends Model
{
    protected $fillable = array('id', 'name', 'age', 'active');
    protected $casts = array('age' => 'integer', 'active' => 'boolean');

    /**
     * @return \Rougin\Ezekiel\Active\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(__NAMESPACE__ . '\Post');
    }

}

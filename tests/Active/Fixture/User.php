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
     * @param string $value
     *
     * @return string
     */
    public function getNameAttribute($value)
    {
        return $value;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(__NAMESPACE__ . '\Post');
    }

    /**
     * @return \Rougin\Ezekiel\Active\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(__NAMESPACE__ . '\Profile');
    }

    /**
     * @return \Rougin\Ezekiel\Active\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(__NAMESPACE__ . '\Tag', 'post_tag', 'post_id', 'tag_id');
    }
}

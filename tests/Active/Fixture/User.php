<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @property integer                                     $id
 * @property string                                      $name
 * @property integer                                     $age
 * @property boolean                                     $active
 * @property string|null                                 $created_at
 * @property string|null                                 $updated_at
 * @property \Rougin\Ezekiel\Active\Fixture\Post[]       $posts
 * @property \Rougin\Ezekiel\Active\Fixture\Profile|null $profile
 * @property \Rougin\Ezekiel\Active\Fixture\Tag[]        $tags
 * @property boolean                                     $exists
 *
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class User extends Model
{
    /**
     * @var array<integer, string>
     */
    protected $fillable = array('id', 'name', 'age', 'active');

    /**
     * @var array<string, string>
     */
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

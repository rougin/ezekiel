<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @property integer                                  $id
 * @property integer                                  $user_id
 * @property string                                   $title
 * @property \Rougin\Ezekiel\Active\Fixture\User|null $user
 * @property \Rougin\Ezekiel\Active\Fixture\Tag[]     $tags
 *
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Post extends Model
{
    /**
     * @var array<integer, string>
     */
    protected $fillable = array('id', 'user_id', 'title');

    /**
     * @var array<string, string>
     */
    protected $casts = array('user_id' => 'integer');

    /**
     * @return \Rougin\Ezekiel\Active\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(__NAMESPACE__ . '\Tag', 'post_tag', 'post_id')
            ->withPivot('extra');
    }

    /**
     * @return \Rougin\Ezekiel\Active\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(__NAMESPACE__ . '\User');
    }
}

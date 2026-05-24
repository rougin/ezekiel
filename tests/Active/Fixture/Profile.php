<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @property integer                                  $id
 * @property integer                                  $user_id
 * @property string                                   $bio
 * @property \Rougin\Ezekiel\Active\Fixture\User|null $user
 *
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Profile extends Model
{
    /**
     * @var array<integer, string>
     */
    protected $fillable = array('id', 'user_id', 'bio');

    /**
     * @return \Rougin\Ezekiel\Active\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(__NAMESPACE__ . '\User');
    }
}

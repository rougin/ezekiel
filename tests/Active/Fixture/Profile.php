<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Profile extends Model
{
    /**
     * @var string[]
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
